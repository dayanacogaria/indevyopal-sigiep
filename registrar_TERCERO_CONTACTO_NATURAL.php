<?php 
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#02/08/2018 |Erica G. | Correo Electrónico - Arreglar Código
#######################################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php';  
$_SESSION['perfil'] = "N"; //Natural.
$_SESSION['url'] = "registrar_TERCERO_ClIENTE_NATURAL.php";

#****** Tipo Identificación *********#
$idents = "SELECT Id_Unico, Nombre FROM gf_tipo_identificacion ORDER BY Nombre ASC";
$tipoI=   $mysqli->query($idents);
#****** Tipo Régimen  *********#
$regimenes = "SELECT Id_Unico, Nombre FROM gf_tipo_regimen ORDER BY Nombre ASC";
$regimen = $mysqli->query($regimenes);
?>
    <title>Registrar Contacto Natural</title>
    <link href="css/select/select2.min.css" rel="stylesheet">
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <style>
        label #tipoI-error,#numId-error,#correo-error, #primerN-error, #primerA-error{
            display: block;
            color: #bd081c;
            font-weight: bold;
            font-style: italic;
        }
    </style>
    <script>
        $().ready(function () {
            var validator = $("#form").validate({
                ignore: "",
                errorPlacement: function (error, element) {
                    $(element)
                        .closest("form")
                        .find("label[for='" + element.attr("id") + "']")
                        .append(error);
                },
            });
            $(".cancel").click(function () {
                validator.resetForm();
            });
        });
    </script>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top:-20px">
                <h2 class="tituloform" align="center">Registrar Contacto Natural</h2>
                <a href="TERCERO_CONTACTO_NATURAL.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">  R</h5>
                <div class="client-form contenedorForma">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_TERCERO_CONTACTO_NATURALJson.php">
                        <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                        <div class="form-group">
                            <label for="tipoI" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Identificación:</label>
                            <select name="tipoI" id="tipoI" class="select2_single form-control" title="Seleccione el tipo identificación" required>
                                <option value="">Tipo identificación</option>
                                <?php while ($row = mysqli_fetch_assoc($tipoI)) { ?>
                                    <option value="<?php echo $row['Id_Unico'] ?>"><?php echo ucwords((mb_strtolower($row['Nombre'])));
                            } ?></option>;
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="numId" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                            <input type="number" name="numId" id="numId" class="form-control" onblur="return existente()" onkeyup="this.value = this.value.slice(0,20)"  maxlength="20" title="Ingrese el número identificación" onkeypress="return txtValida(event,'num')"   placeholder="Número identificación" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="primerN" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Primer Nombre:</label>
                            <input type="text" name="primerN" id="primerN" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese primer nombre" onkeypress="return txtValida(event,'car')"   placeholder="Primer Nombre" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="segundoN" class="col-sm-5 control-label">Segundo Nombre:</label>
                            <input type="text" name="segundoN" id="segundoN" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese segundo nombre" onkeypress="return txtValida(event,'car')"   placeholder="Segundo Nombre">
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="primerA" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Primer Apellido:</label>
                            <input type="text" name="primerA" id="primerA" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese primer apellido" onkeypress="return txtValida(event,'car')"   placeholder="Primer Apellido" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px;">
                            <label for="segundoA" class="col-sm-5 control-label">Segundo Apellido:</label>
                            <input type="text" name="segundoA" id="segundoA" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese segundo apellido" onkeypress="return txtValida(event,'car')" placeholder="Segundo Apellido">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="correo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Correo Electrónico:</label>
                            <input type="email" name="correo" id="correo" class="form-control" maxlength="500" title="Ingrese Correo Electrónico" placeholder="Corrreo Electrónico" >
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                        </div>
                        <input type="hidden" name="id" id="id">
                        <div class="texto" style="display:none"></div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>
            </div>
            <div class="col-sm-7 col-sm-3" style="margin-top:-22px">
                <h2 class="titulo" align="center" >Información Adicional</h2>
                <div align="center">
                    <button class="btn btnInfo btn-primary" disabled="true">DIRECCIÓN</button><br/>
                    <a href="GF_TELEFONO.php"><button class="btn btnInfo btn-primary" <?php if (!isset($_SESSION['id_tercero'])) {
                                    echo ' disabled title="Debe primero ingresar un contacto natural."';
                                } ?> disabled="true" >TELÉFONO</button></a><br/>

                    <a href="GF_CONDICION_TERCERO.php"><button class="btn btnInfo btn-primary" <?php if (!isset($_SESSION['id_tercero'])) {
                                    echo ' disabled title="Debe primero ingresar un contacto natural."';
                                } ?> disabled="true">CONDICIÓN</button></a><br/>
                    <button class="btn btnInfo btn-primary" disabled="true">PERFIL CONDICIÓN</button><br/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Este número de identificación  ya existe.¿Desea actualizar la información?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" class="btn" style="color: #000; margin-top: 2px"  data-dismiss="modal" id="ver2">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal2" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Seleccione un Tipo Identificación.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal4" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
              </div>
              <div class="modal-body" style="margin-top: 8px">
                  <p>Este número de identificación no se puede registrar.<br/> 
                      Tiene un perfil no asociado al perfil a registrar.</p>
              </div>
              <div id="forma-modal" class="modal-footer">
                  <button type="button" id="ver4" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
              </div>
            </div>
        </div>
    </div>
    <?php require_once('footer.php'); ?>
    <script src="js/select/select2.full.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/md5.js"></script>
    <script>
      $(document).ready(function () {
          $(".select2_single").select2({
              allowClear: true
          });
      });
    </script>
    <script type="text/javascript">
        function existente(){
            var tipoD = document.form.tipoI.value;    
            var numI = document.form.numId.value;
            var result = '';
            if(tipoD == null || tipoD == '' || tipoD == "Tipo Identificación" || numI == null || numI == ""){
              $("#myModal2").modal('show');
            }else{
                $.ajax({
                data: {"numI": numI,perfil:10, action:2},
                type: "POST",
                url: "jsonPptal/gf_tercerosJson.php",
                success: function (data) {
                    var resultado = JSON.parse(data);
                    var rta = resultado["rta"];
                    var id  = resultado["id"];
                    console.log(data);
                    if (rta == 0) {
                        if(id!=0){
                            $("#id").val(md5(id));
                            $("#myModal1").modal('show');
                        }
                    } else {
                        $("#myModal4").modal('show');
                        $('#ver4').click(function () {
                            $("#numId").val('');
                        });
                    }                   
                }
            });
            }
        }
    </script>
    <script type="text/javascript">
      $('#ver1').click(function(){
        var id = document.form.id.value;
            document.location = 'modificar_TERCERO_CONTACTO_NATURAL.php?id_ter_cont_nat=' + id;
        });

    </script>

</body>
    </html>