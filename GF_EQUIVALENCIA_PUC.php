<?php
require_once ('Conexion/conexion.php');
require_once 'head_listar.php'; 

?>
<title>Equivalencia PUC</title>
<style>
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
  .form-control{
      font-size: 11px;
  }
</style>
</head>
<body> 
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
    <div class="container-fluid text-center">
	<div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top:-3px">Equivalencia PUC</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonProcesos/registrar_GG_PROCESOJson.php">
                    <div class="form-group form-inline text-center" style="margin-top:20px">
                        <!--Identificador-->
                        <div class="form-group form-inline">
                            <label style="width:200px;" for="tipoEquivalencia" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Equivalencia:</label>
                            <select name="tipoEquivalencia" id="tipoEquivalencia" style="width: 200px" class="select2_single form-control" title="Seleccione Tipo Equivalencia" required  style="display: inline;">
                                    <?PHP if(empty($_SESSION['tipoEquivalencia'])) { ?>
                                        <option value="">Tipo Equivalencia</option>
                                        <?php #TIPO EQUIVALENCIA 
                                            $teq="SELECT id_unico, nombre FROM gf_tipo_equivalencia_puc ORDER BY nombre ASC";
                                            $teq=$mysqli->query($teq);?>
                                    <?php } else { 
                                        $ids= $_SESSION['tipoEquivalencia'];
                                        $pm= "SELECT id_unico, nombre FROM gf_tipo_equivalencia_puc WHERE id_unico='$ids'";
                                        $pm = $mysqli->query($pm);
                                        $pm= mysqli_fetch_row($pm);
                                        ?>
                                        <option value="<?php echo $pm[0]?>"><?php echo ucwords(strtolower($pm[1]));?></option>
                                    <?php #TIPO EQUIVALENCIA 
                                            $teq="SELECT id_unico, nombre FROM gf_tipo_equivalencia_puc WHERE id_unico != '$pm[0]' ORDER BY nombre ASC";
                                            $teq=$mysqli->query($teq);
                                        }?>
                                    <?php while($row = mysqli_fetch_row($teq)){?>
                                    <option value="<?php echo $row[0] ?>"><?php echo ucwords(strtolower($row[1]));}?></option>;
                            </select>
                        </div>
                    </div>
                </form>
                </div>
                <script>
                    $("#tipoEquivalencia").change(function() {
                       var tipoEquivalencia = $("#tipoEquivalencia").val();
                        if (tipoEquivalencia =='' || tipoEquivalencia=="" ){
                            
                        } else {
                        var form_data = { case: 14, id:tipoEquivalencia};
                        
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
                    });
                </script>
                <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Cuenta</strong></td>
                                    <td><strong>Cuenta Equivalente</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th width="50%">Cuenta</th>
                                    <th width="50%">Cuenta Equivalente</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($_SESSION['tipoEquivalencia'])) { 
                                 $id=$_SESSION['tipoEquivalencia']; 
                                 $parm= "SELECT parametrizacion FROM gf_tipo_equivalencia_puc WHERE id_unico='$id'";
                                 $parm= $mysqli->query($parm);
                                 $param= mysqli_fetch_row($parm);
                                 $param= $param[0];
                                 $cuenta = "SELECT id_unico, codi_cuenta, nombre "
                                         . "FROM gf_cuenta "
                                         . "WHERE movimiento ='1' "
                                         . "OR centrocosto='1' "
                                         . "OR auxiliartercero='1' "
                                         . "OR auxiliarproyecto='1' "
                                         . "ORDER BY codi_cuenta ASC " ;
                                 $cuenta = $mysqli->query($cuenta);
                                 
                                 while ($rowC1= mysqli_fetch_row($cuenta)) {
                                    $equivalencia="SELECT ep.cuenta_equivalente, c.codi_cuenta, c.nombre, ep.id_unico "
                                            . "FROM gf_equivalencia_puc ep LEFT JOIN gf_cuenta c ON ep.cuenta_equivalente = c.id_unico "
                                            . "WHERE tipo_equivalencia='$id' AND cuenta = '$rowC1[0]'"; 
                                    $equivalencia = $mysqli->query($equivalencia);
                                    if(mysqli_num_rows($equivalencia)>0) {
                                    $equivalencia= mysqli_fetch_row($equivalencia);} else {$equivalencia='';}?>
                            
                                <tr>
                                    <td style="display: none;"></td>                                    
                                    <td>
                                        <?php if(empty($equivalencia[0])) { ?>
                                        <a href="#" onclick="guardar(<?php echo $rowC1[0]?>)"><i title="Guardar" class="glyphicon glyphicon-floppy-disk" ></i></a>
                                        <?php } else { ?>
                                        <a  href="#" onclick="javascript:eliminar(<?php echo $equivalencia[3]?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a href="#" onclick="modificar(<?php echo $equivalencia[3].','.$equivalencia[0]?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php echo $rowC1[1].' - '. ucwords(strtolower($rowC1[2]))?>
                                    </td>
                                    <td>
                                        
                                        <?php if(empty($equivalencia[0])) { 
                                            $cuentaE= "SELECT id_unico, codi_cuenta, nombre "
                                                    . "FROM gf_cuenta WHERE parametrizacionanno = '$param' ORDER BY codi_cuenta ASC ";
                                                  $cuentaE=$mysqli->query($cuentaE);?>
                                        <select name="cuenta2<?php echo $rowC1[0]?>" id="cuenta2<?php echo $rowC1[0]?>" style="width: 250px" class="form-control" onclick="return select(<?php echo $rowC1[0]?>);" title="Seleccione Cuenta Equivalente" required  style="display: inline;">
                                                    <option value="">Cuenta Equivalente</option>
                                                    <?php while ($rowCe = mysqli_fetch_row($cuentaE)) {?>
                                                    <option value="<?php echo $rowCe[0]?>"><?php echo $rowCe[1].' - '.ucwords(strtolower($rowCe[2]));?></option>
                                                    <?php }?>
                                            </select>
                                        <script>
                                                function select(id){
                                                    var flujo = 'cuenta2'+id;
                                                    $(".select2_single, #"+flujo).select2();
                                                }
                                         </script>
                                            <?php } else { 
                                                
                                                echo $equivalencia[1].' - '.ucwords(strtolower($equivalencia[2]));
                                            } ?>
                                    </td>
                                </tr>
                                 <?php } ?>
                            
                            <?php } ?>    
                            </tbody>
                         </table>
                    </div>
                </div>
            </div>
	</div>
    </div>
<script src="js/select/select2.full.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<!-- select2 -->
 

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>
  <div class="modal fade" id="myModalGB" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Información guardada correctamente.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="verGB" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModalNG" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se ha podido guardar la información.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="verGM" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de equivalencia PUC?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
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
                <p>Información eliminada correctamente</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                <p>No se pudo eliminar la información, el registro seleccionado esta siendo usado por otra dependencia.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
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
          <button type="button" id="ver5" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
           <p>La información no se ha podido modificar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver6" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
 <!--  MODAL y opcion  MODIFICAR  informacion  -->  
<div class="modal fade" id="myModalUpdate" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content client-form1">
      <div id="forma-modal" class="modal-header">       
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
      </div>
        <?php 
                #CUENTA DOS
                $id1=$_SESSION['tipoEquivalencia']; 
                 $parm1= "SELECT parametrizacion FROM gf_tipo_equivalencia_puc WHERE id_unico='$id1'";
                 $parm1= $mysqli->query($parm1);
                 $param1= mysqli_fetch_row($parm1);
                 $param1= $param1[0];
                $cuenta2m= "SELECT id_unico, codi_cuenta, nombre "
                        . "FROM gf_cuenta WHERE parametrizacionanno = '$param1' ORDER BY codi_cuenta ASC";
                $cuenta2m = $mysqli->query($cuenta2m);
        ?>
      <div class="modal-body ">
          <form  name="formMod" id="formMod" method="POST" action="javascript:modificarItem()">
            <input type="hidden" name="idm" id="idm">
            <div class="form-group" style="margin-top: 13px;">
                <label for="cuenta2m" class="control-label" style="width: 150px"><strong style="color:#03C1FB;">*</strong>Cuenta Equivalente:</label>
                <select name="cuenta2m" id="cuenta2m"  style="width: 250px" class="select2_single form-control" title="Seleccione Equivalente" required  style="display: inline;">
                    <?php while($row2m = mysqli_fetch_row($cuenta2m)){?>
                    <option value="<?php echo $row2m[0] ?>"><?php echo ucwords((strtolower($row2m[1].' - '.$row2m[2])));}?></option>;
                </select>
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
 
   <script src="js/select/select2.full.js"></script>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true,
      });
    });
  </script>
  <script>
          function guardar(id){
              var cuenta= id;
              var tipo = <?php echo $_SESSION['tipoEquivalencia'];?>;
              var cuentaE= document.getElementById('cuenta2'+id).value;
              if (cuenta!='' && tipo!='' && cuentaE!=''){
              var form_data = { 
                    cuenta:cuenta,
                    tipo: tipo,
                    cuentaE: cuentaE 
                };     
                 $.ajax({
                   type: "POST",
                   url: "json/registrar_GF_EQUIVALENCIA_PUC.php",
                   data: form_data,
                   success: function(data)
                   {
                       var result = JSON.parse(data);
                        if(result==true){
                            $("#myModalGB").modal('show');
                            $("#verGB").click(function(){
                                document.location.reload();
                              });
                          } else {
                            $("#myModalNG").modal('show');
                            $("#verNG").click(function(){
                                document.location.reload();
                              });
                        }
                   }
                 });
             }
              
          }
  </script>

 
    <script>
          function modificar(id, cuentaE){
              $("#idm").val(id);
              $("#cuenta2m").val(cuentaE);
              $("#myModalUpdate").modal('show');
          }
  </script>
  <script>
  function modificarItem()
    {
      var formData = new FormData($("#formMod")[0]);  
      $.ajax({
          
        type:"POST",
        url:"json/modificar_GF_EQUIVALENCIA_PUCJson.php",
        data:formData,
        contentType: false,
         processData: false,
        success: function (data) {
          result = JSON.parse(data);
          if(result==true){
            $("#myModalUpdate").modal('hide');
            $("#myModal5").modal('show');
            $("#ver5").click(function(){
              
              $("#myModal5").modal('hide');
              document.location.reload();
              
            });
          }else{
             $("#myModalUpdate").modal('hide'); 
            $("#myModal6").modal('show');
            $("#ver6").click(function(){
              
              $("#myModal6").modal('hide');
              document.location.reload();
              
            });
           
          }
        }
      });
    }

        </script>
<script>
        function eliminar(id){
            var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminar_GF_EQUIVALENCIAPUCJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true){
                      $("#myModal1").modal('show');
                      $("#ver1").click(function(){
                          document.location.reload();
                        });
                    } else {
                      $("#myModal2").modal('show');
                      $("#ver2").click(function(){
                          document.location.reload();
                        });
                  }}
              });
          });
        }
</script>
     <?php require_once 'footer.php'; ?>
     


</body>
</html>


