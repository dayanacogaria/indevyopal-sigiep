<?php
    #####################################################################################
    # ********************************* Modificaciones *********************************#
    #####################################################################################
    #30/11/2018 |Nestor B. | Archivo Creado
    ####/################################################################################
    require_once('Conexion/conexion.php');
    require_once('Conexion/ConexionPDO.php');
    require_once('head_listar.php');
    $con    = new ConexionPDO();
    $anno   = $_SESSION['anno'];
    $compania = $_SESSION['compania'];

    @$id_mat = $_REQUEST['id'];
    @$id_proy = $_REQUEST['pro'];
    $valor = $id_proy;
    $proy = "SELECT id_unico, titulo FROM gy_proyecto WHERE md5(id_unico) = '$id_proy'";
    $pro = $mysqli->query($proy);
    $pr  = mysqli_fetch_row($pro);
    
    $matriz = "SELECT   m.id_unico,
                        t.id_unico,
                        t.nombre,
                        r.id_unico,
                        r.nombre,
                        p.id_unico,
                        p.nombre,
                        ti.id_unico,
                        ti.nombre,
                        m.controles_existentes,
                        mi.id_unico,
                        mi.nombre,
                        tr.id_unico,
                        IF(CONCAT_WS(' ',
                            tr.nombreuno,
                            tr.nombredos,
                            tr.apellidouno,
                            tr.apellidodos) 
                        IS NULL OR CONCAT_WS(' ',
                            tr.nombreuno,
                            tr.nombredos,
                            tr.apellidouno,
                            tr.apellidodos) = '',
                            (tr.razonsocial),
                        CONCAT_WS(' ',
                            tr.nombreuno,
                            tr.nombredos,
                            tr.apellidouno,
                            tr.apellidodos)) AS NOMBRE
                FROM gy_matriz_riesgo m
                LEFT JOIN gy_tipo_riesgo t ON m.id_tipo_riesgo = t.id_unico
                LEFT JOIN gy_riesgo r ON m.id_riesgo = r.id_unico
                LEFT JOIN gy_probabilidad p ON m.id_probabilidad = p.id_unico
                LEFT JOIN gy_tipo_impacto ti ON m.id_tipo_impacto = ti.id_unico
                LEFT JOIN gy_mitigacion mi ON m.id_mitigacion = mi.id_unico
                LEFT JOIN gf_tercero tr ON m.id_tercero_responsable = tr.id_unico
                WHERE md5(m.id_unico) = '$id_mat'";
                        
    $res = $mysqli->query($matriz);
    $rowMat = mysqli_fetch_row($res);
?>
<title>Modificar Matriz</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label  #sltRiesgo-error, #sltTRiesgo-error, #sltProba-error, #sltTim-error, 
    #txtControles-error, #sltMit-error, #sltResponsable-error {
    display: block;
     color: #155180;
    font-weight: bold;
    font-style: italic;
    font-size: 10px
}
body{
    font-size: 11px;
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
    }
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<style>
    .form-control {font-size: 12px;}
</style>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once ('menu.php'); ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Matriz de Riesgo</h2>
                <a href="GY_MATRIZ_RIESGO.php?id=<?php echo $id_proy ?>&valor=1" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: White; border-radius: 5px"><?php echo 'Proyecto: '.$pr[1];?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
                        <div class="form-group" style="margin-top: 1%">
                            <input type="hidden" name="txtidM" id="txtidM" value="<?php echo $rowMat[0] ?>"> 
                            <label for="sltTRiesgo" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Tipo Riesgo:</label>
                            <div class="classTipoR">
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <?php
                                        $tipoR = "SELECT id_unico , nombre FROM gy_tipo_riesgo 
                                                WHERE compania = '$compania' AND id_unico != '$rowMat[1]'";
                                        
                                        $triesgo = $mysqli->query($tipoR);
                                    ?>
                                    <select id="sltTRiesgo" name="sltTRiesgo" class="form-control select2_single" title="Sleccione el Tipo Riesgo" required >
                                        <option value="<?php echo $rowMat[1] ?>"><?php echo $rowMat[2] ?></option>
                                        <?php
                                            while($rowTR = mysqli_fetch_row($triesgo)){
                                        ?>
                                                <option value="<?php echo $rowTR[0] ?>"><?php echo $rowTR[1] ?></option>
                                        <?php
                                            }
                                        ?>
                                    </select>
                                    
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group"  align="left">
                            <label for="sltRiesgo" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Riesgo:</label>
                            <div class="classRiesgo">   
                                <div class="col-sm-6 col-md-6 col-lg-6">    
                                    <select id="sltRiesgo" name="sltRiesgo" class="form-control select2_single" title="Seleccione el Riesgo" required >
                                        <option value="<?php echo $rowMat[3] ?>"><?php echo $rowMat[4] ?></option>
                                    </select>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            $(".classTipoR select").change(function(){
                                                var form_data = {
                                                    is_ajax: 1,
                                                    id_TipoR: +$(".classTipoR select").val()
                                                };
                                                $.ajax({
                                                    type: "POST",
                                                    url: "buscar_GY_RIESGO.php",
                                                    data: form_data,
                                                    success: function(response){
                                                        $('.classRiesgo select').html(response).fadeIn();
                                                        $('#sltRiesgo').css('display','none');
                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sltProba" type="date" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Probabilidad:</label>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <select id="sltProba" name="sltProba" class="form-control select2_single" title="Seleccione la Probabilidad" required>
                                    <option value="<?php echo $rowMat[5]  ?>"><?php echo $rowMat[6]  ?></option>
                                    <?php
                                        $probabi = "SELECT id_unico, nombre FROM gy_probabilidad WHERE compania = '$compania' AND id_unico != '$rowMat[5]'";
                                        $proba = $mysqli->query($probabi);

                                        while($rowP = mysqli_fetch_row($proba)){
                                    ?>
                                            <option value="<?php echo $rowP[0] ?>"><?php echo $rowP[1] ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>    
                        </div>

                        <div class="form-group">
                            <label for="sltTim" type="text" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Impacto:</label>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <select id="sltTim" name="sltTim" class="form-control select2_single " title="Seleccione el tipo de Impacto" required>
                                    <option value="<?php echo $rowMat[7]  ?>"><?php echo $rowMat[8]  ?></option>
                                    <?php
                                        $probabi = "SELECT id_unico, nombre FROM gy_tipo_impacto WHERE compania = '$compania' AND id_unico != '$rowMat[7]'";
                                        $proba = $mysqli->query($probabi);

                                        while($rowP = mysqli_fetch_row($proba)){
                                    ?>
                                            <option value="<?php echo $rowP[0] ?>"><?php echo $rowP[1] ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>    
                        </div>
                                        
                        <div class="form-group">

                            <label for="txtControles" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Controles Existentes:</label>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <input name="txtControles" id="txtControles" title="Ingrese los controles existentes " type="text" class="form-control "  value="<?php echo $rowMat[9] ?>" required>
                            </div>    
                        </div>
                        
                       <div class="form-group">
                            <label for="sltMit" type="text" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Mitigación:</label>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <select id="sltMit" name="sltMit" class="form-control select2_single" title="Seleccione la mitigación" required>
                                    <option value="<?php echo $rowMat[10]  ?>"><?php echo $rowMat[11]  ?></option>
                                    <?php
                                        $probabi = "SELECT id_unico, nombre FROM gy_mitigacion WHERE compania = '$compania' AND id_unico != '$rowMat[10]'";
                                        $proba = $mysqli->query($probabi);

                                        while($rowP = mysqli_fetch_row($proba)){
                                    ?>
                                            <option value="<?php echo $rowP[0] ?>"><?php echo $rowP[1] ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>    
                        </div>
                        
                        <div class="form-group"  align="left">
                            <label for="sltResponsable" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Responsable:</label>
                            <?php 
                                $responsable = "SELECT tr.id_unico,
                                                                   IF(CONCAT_WS(' ',
                                                                     tr.nombreuno,
                                                                     tr.nombredos,
                                                                     tr.apellidouno,
                                                                     tr.apellidodos) 
                                                                     IS NULL OR CONCAT_WS(' ',
                                                                     tr.nombreuno,
                                                                     tr.nombredos,
                                                                     tr.apellidouno,
                                                                     tr.apellidodos) = '',
                                                                     (tr.razonsocial),
                                                                     CONCAT_WS(' ',
                                                                     tr.nombreuno,
                                                                     tr.nombredos,
                                                                     tr.apellidouno,
                                                                     tr.apellidodos)) AS NOMBRE
                                                                FROM gf_tercero tr
                                                                LEFT JOIN gy_tercero_proyecto tp ON tp.id_tercero = tr.id_unico
                                                                WHERE md5(tp.id_proyecto) = '$valor' AND tr.id_unico != '$rowMat[12]'";
                                $resp = $mysqli->query($responsable);
                            ?> 
                            
                            <div class="col-sm-6 col-md-6 col-lg-6">    
                                <select  id="sltResponsable" name="sltResponsable" class="form-control select2_single" title="Seleccione el Responsable"  required="">
                                    <option value="<?php echo $rowMat[12] ?>"><?php echo $rowMat[13] ?></option>
                                    <?php 
                                        while($rowTR = mysqli_fetch_row($resp)){
                                            echo "<option value=".$rowTR[0].">".$rowTR[1]."</option>";
                                        }
                                    ?>                     
                                </select>
                            </div>    
                        </div>
                        <div class="form-group" style="margin-top: 20px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                    
                    <script>
                        function modificar(){
                            jsShowWindowLoad('Modificando Datos ...');
                            var formData = new FormData($("#form")[0]);
                            $.ajax({
                                type: 'POST',
                                url: "jsonProyecto/gy_matriz_riesgoJson.php?action=3",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function(response)
                                {
                                    jsRemoveWindowLoad();
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje").html('Información Modificada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            //document.location='listar_GY_ACTIVIDAD.php';
                                            window.history.go(-1);
                                        })
                                    } else {
                                        $("#mensaje").html('No Se Ha Podido Modificar Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            $("#modalMensajes").modal("hide");
                                        })

                                    }
                                }
                            });
                        }
                    </script>
                   
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
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
                    <p>¿Desea Eliminar El Registro Seleccionado?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="aceptarE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="cancelarE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once ('footer.php'); ?>
    <script src="js/select/select2.full.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script>
      $(document).ready(function () {
          $(".select2_single").select2({
              allowClear: true
          });
      });
    </script>
</body>
</html>



