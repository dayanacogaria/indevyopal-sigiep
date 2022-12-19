<?php 
require_once ('Conexion/conexion.php');
require_once 'head_listar.php';


?>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
label#responsable-error, #estado-error, #tipoProceso-error, #fecha-error, #identificador-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}
body{
    font-size: 12px;
}
 table.dataTable thead th,table.dataTable thead td
  {
    padding: 1px 18px;
  }

  table.dataTable tbody td,table.dataTable tbody td
  {
    padding: 1px;
  }
  .dataTables_wrapper .ui-toolbar
  {
    padding: 2px;
    font-size: 12px;
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
<title>Lectura</title>
</head>
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-9 text-left" style="margin-left: -16px;margin-top: -22px; ">
            <h2 class="tituloform" align="center" >Lectura</h2>
             
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data">
                    <p align="center" style="margin-bottom: 20px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group form-inline text-center" style="margin-left:0px; margin-top:-10px">
                       <?php if(empty($_SESSION['periodo'])){ ?>
                        <!--Periodo-->
                        <?php                 
                        $periodo = "SELECT DISTINCT "
                                . "p.id_unico, p.nombre FROM gp_periodo p ORDER BY p.nombre ASC";
                        $periodo = $mysqli->query($periodo);?>

                        <div class="form-group form-inline text-center" style="margin-left:10px">
                            <label style="width:100px;" for="periodo" class="control-label"><strong style="color:#03C1FB;">*</strong>Periodo:</label>
                            <input type="hidden" name="periodo" id="periodo" required="required" title="Seleccione periodo">
                            <select style="width:200px;" name="periodo1" id="periodo1" required="required" class="select2_single form-control" title="Seleccione Periodo" required="required">
                                <option value="">Periodo</option>
                                <?php while($row2 = mysqli_fetch_row($periodo)){?>
                                <option value="<?php echo $row2[0] ?>"><?php echo ucwords((strtolower($row2[1])));}?></option>;
                            </select> 
                        </div>
                       <?php } else { ?>
                        <!--Periodo-->
                        <?php
                            $id = $_SESSION['periodo'];
                            $valor ="SELECT id_unico, nombre FROM gp_periodo WHERE id_unico = '$id'";
                            $valor = $mysqli->query($valor);
                            $valor = mysqli_fetch_row($valor);
                            $periodo = "SELECT DISTINCT "
                                . "p.id_unico, p.nombre FROM gp_periodo p WHERE id_unico != '$id' ORDER BY p.nombre ASC";
                            $periodo = $mysqli->query($periodo);
                                ?>
                        <div class="form-group form-inline text-center" style="margin-left:10px">
                            <label style="width:100px;" for="periodo" class="control-label"><strong style="color:#03C1FB;">*</strong>Periodo:</label>
                            <input type="hidden" name="periodo" id="periodo" required="required" title="Seleccione periodo" value="<?php echo $valor[0];?>">
                            <select style="width:200px;" name="periodo1" id="periodo1" required="required" class="select2_single form-control" title="Seleccione Periodo" required="required">
                                <option value="<?php echo $valor[0]?>"><?php echo ucwords(strtolower($valor[1]));?></option>
                                <?php while($row2 = mysqli_fetch_row($periodo)){?>
                                <option value="<?php echo $row2[0] ?>"><?php echo ucwords((strtolower($row2[1])));}?></option>;
                            </select> 
                        </div>
                       <?php } ?>
                        <div class="form-group" style="margin-top:18px; margin-left:35px;">
                                <a href="#" onclick="javascript:vaciar()" class="btn btn-primary sombra" title="Vaciar" style="margin-left: 10px"> <i class="glyphicon glyphicon-remove" ></i></a>
                            </div>
                        
                    </div>
                </form>
            </div>
            <script>
                 $("#periodo1").change(function() {
                     var periodo = document.getElementById('periodo1').value;
                    var form_data = { case: 9, periodo : periodo};
                        
                         $.ajax({
                           type: "POST",
                           url: "consultasBasicas/busquedas.php",
                           data: form_data,
                           success: function(data)
                           {
                             document.location.reload();                             
                           }
                         });
                });
            </script>
            <script>
                    function vaciar(){
                        var form_data = { case: 10};
                        
                         $.ajax({
                           type: "POST",
                           url: "consultasBasicas/busquedas.php",
                           data: form_data,
                           success: function(data)
                           {
                             document.location.reload();                             
                           }
                         });
                    }
                </script>
            
            <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <td style="display: none;">Identificador</td>
                                <td width="30px"></td>
                                <td><strong>Referencia</strong></td>
                                <td><strong>Valor</strong></td>
                            </tr>
                            <tr>
                                <th style="display: none;">Identificador</th>
                                <th width="7%"></th>
                                <th>Referencia</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <?php if(empty($_SESSION['periodo'])){ ?>
                        <tbody>
                        </tbody>
                        <?php } else {  
                            $id = $_SESSION['periodo'];
                            #LISTAR MEDIDORES
                              $listarm = "SELECT
                                                uvms.id_unico,
                                                m.referencia 
                                              FROM
                                                gp_unidad_vivienda_medidor_servicio uvms
                                              LEFT JOIN
                                                gp_medidor m ON m.id_unico = uvms.medidor";                                              
                            $listarm = $mysqli->query($listarm);
                            while ($rowm = mysqli_fetch_row($listarm)) {
                                $lectura="SELECT l.id_unico, l.valor "
                                            . "FROM gp_lectura l "
                                            . "WHERE l.unidad_vivienda_medidor_servicio='$rowm[0]' AND periodo = '$id'"; 
                                $lectura = $mysqli->query($lectura);
                                if(mysqli_num_rows($lectura)>0) {
                                    $lectura= mysqli_fetch_row($lectura);
                                } else {
                                    $lectura='';
                                } ?>
                                 <tr>
                                    <td style="display: none;"></td>                                    
                                    <td>
                                        <?php if(empty($lectura[0])) { ?>
                                        <a href="#" onclick="guardar(<?php echo $rowm[0].','.$id?>)"><i title="Guardar" class="glyphicon glyphicon-floppy-disk" ></i></a>
                                        <?php } else { ?>
                                        <a  href="#" onclick="javascript:eliminar(<?php echo $lectura[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a href="#" onclick="modificar(<?php echo $lectura[0].','.$lectura[1];?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                        <?php } ?>
                                    </td>
                                    <td>
                                         <?php echo ucwords(strtolower($rowm[1]));?>       
                                    </td>
                                    <td>
                                        
                                        <?php if(empty($lectura[0])) { ?>
                                        <input type="text" name="valor<?php echo $rowm[0]?>"  id="valor<?php echo $rowm[0]?>" class="form-control" title="Ingrese el valor"  placeholder="Valor" required style="width: 250px; display: inline" onkeypress="return txtValida(event, 'num')">
                                        <label id="labelError<?php echo $rowm[0]?>" name="labelError<?php echo $rowm[0]?>" style="color: #155180; font-weight: normal; font-style: italic;"></label>
                                            <?php } else { 
                                                
                                                echo $lectura[1];
                                            } ?>
                                    </td>
                                    <script>
                                          $("#valor<?php echo $rowm[0]?>").change(function() {
                                            var id = <?php echo $rowm[0]?>;
                                            var valor = document.getElementById('valor<?php echo $rowm[0]?>').value;
                                            var periodo = <?php echo $_SESSION['periodo'];?>;
                                            var form_data={
                                                case:6,
                                                id:id,
                                                valor:valor, 
                                                periodo: periodo
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasBasicas/busquedas.php",
                                                data:form_data,
                                                success: function (data) { 
                                                    
                                                    var resultado = JSON.parse(data);
                                                    if (resultado == 'null' || resultado== null || resultado =='' || resultado ==""){resultado=0;}
                                                    var valor1 = (parseInt(valor));
                                                    var resultado1 = parseInt(resultado);
                                                    if(valor1 >= resultado1){
                                                        document.getElementById('valor<?php echo $rowm[0]?>').value= valor;
                                                        document.getElementById('labelError<?php echo $rowm[0]?>').innerHTML= '';
                                                    } else {
                                                        document.getElementById('valor<?php echo $rowm[0]?>').value= '';
                                                        document.getElementById('labelError<?php echo $rowm[0]?>').innerHTML= 'Valor Inválido';

                                                    }
                                                }
                                            });
                                        });
                                    </script>
                            <?php }?>
                        
                        <?php } ?>
                    </table>
                </div>
            </div>
            <!--Modales ingresar valor -->
                <div class="modal fade" id="myModalValor" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>Ingrese un valor.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="valorMod" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!--Modales registro -->
                <div class="modal fade" id="myModal" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>Información guardada correctamente.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModal1" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>El registro ingresado ya existe.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModal2" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>No se ha podido guardar la informaci&oacuten.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Fin modales registro-->
                <!--Modales eliminar-->
                <div class="modal fade" id="myModalEliminar" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>¿Desea eliminar el registro seleccionado de lectura?</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="verE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModal3" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>Información eliminada correctamente</p>
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
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>No se pudo eliminar la información, el registro seleccionado esta siendo usado por otra dependencia.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver4" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Modales modicar-->
                <div class="modal fade" id="myModal5" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>Información modificada correctamente.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver5" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModal6" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>El registro ingresado ya existe.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver6" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModal7" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>No se ha podido modificar la informaci&oacuten.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver7" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModalUpdate" role="dialog" align="center" >
                    <div class="modal-dialog">
                      <div class="modal-content client-form1">
                        <div id="forma-modal" class="modal-header">       
                          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
                        </div>
                        <div class="modal-body ">
                            <form  name="formMod" id="formMod" method="POST" action="javascript:guardarCambios()">
                              <input type="hidden" name="idm" id="idm">
                              <div class="form-group" style="margin-top: 13px;">
                                  <label for="cuenta2m" class="control-label" style="width: 150px"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                                  <input type="text" name="valorm" id="valorm" required="required" class="form-control" onkeypress="return txtValida(event, 'num')">
                              </div>
                        </div>

                        <div id="forma-modal" class="modal-footer">
                            <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
                          <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
                        </div>
                        </form>
                      </div>
                    </div>
                  </div> 
                <!--Funcion eliminar-->
                <script>
                    function eliminar(id){
                        $("#myModalEliminar").modal('show');
                        $("#verE").click(function(){
                             $("#myModalEliminar").modal('hide');
                             $.ajax({
                                 type:"GET",
                                 url:"json/eliminar_GP_LECTURAJson.php?id="+id,
                                 success: function (data) {
                                 result = JSON.parse(data);
                                 if(result==true){
                                     $("#myModal3").modal('show');
                                     $("#ver3").click(function(){
                                       document.location.reload(); 
                                   });
                                 }else{
                                     $("#myModal4").modal('show');
                                     $("#ver4").click(function(){
                                       $("#myModal4").modal('hide');
                                   });
                                 }}
                             });
                         });
                    }
                </script>
                <!--Función guardar-->
                <script>
                    function guardar(id, periodo){
                        var valor = document.getElementById("valor"+id).value;
                        var periodo = periodo;
                        var uvms = id;
                         if(valor==''){
                             $("#myModalValor").modal('show');
                                $('#valorMod').click(function(){
                                    $("#myModalValor").modal('hide');
                                });
                         } else {
                         var form_data = {
                            is_ajax:1,
                            iduvms:uvms,
                            periodo:periodo,
                            valor:valor
                        };
                        var result='';
                        $.ajax({
                            type: 'POST',
                            url: "json/registrar_GP_LECTURAMJson.php",
                            data:form_data,
                            success: function (data) {
                                result = JSON.parse(data);                        
                                if (result==true) {
                                    $("#myModal").modal('show');
                                    $('#ver').click(function(){
                                        $("#myModal").modal('hide');
                                       document.location.reload(); 
                                    });
                                }else {                                
                                    if(result=='3'){
                                        $("#myModal1").modal('show');
                                        $('#ver1').click(function(){
                                            $("#myModal1").modal('hide');
                                        });
                                    }else{
                                        $("#myModal2").modal('show'); 
                                        $('#ver2').click(function(){
                                            $("#myModal2").modal('hide');
                                        });
                                    }
                                }                                                                        
                            }
                        });
                    }
                }
                    
                        
                </script>
                <script>
                    function modificar(id, valor){
                        $("#idm").val(id);
                        $("#valorm").val(valor);
                        $("#myModalUpdate").modal('show');      
                    }
                </script>
                <script>
                    function guardarCambios(){
                           var formData = new FormData($("#formMod")[0]);  
      
                            var result='';
                            $.ajax({
                                type: 'POST',
                                url: "json/modificar_GP_LECTURAMJson.php",
                                data:formData,
                                contentType: false,
                                processData: false,
                                success: function (data) {
                                    result = JSON.parse(data);                        
                                    if (result==true) {
                                        $("#myModalUpdate").modal('hide'); 
                                        $("#myModal5").modal('show');
                                        $('#ver5').click(function(){
                                            $("#myModal5").modal('hide');
                                            document.location.reload(); 
                                        });
                                    }else {  
                                        $("#myModalUpdate").modal('hide'); 
                                        $("#myModal7").modal('show'); 
                                        $('#ver7').click(function(){
                                            $("#myModal7").modal('hide');
                                          document.location.reload(); 
                                        });
                                    }                                                                        
                                }
                            });
                    }
                </script>
        </div>
    </div>
</div>
<?php require_once 'footer.php';?>
</body>
<script src="js/select/select2.full.js"></script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
  </script>
  