<?php 
  //llamado a la clase de conexion
  require_once 'Conexion/conexion.php';
  require_once 'head_listar.php';

$compania   = $_SESSION['compania'];
$id_usuario = $_SESSION['id_usuario'];
$id = $_SESSION['usuario_tercero'];
$datosTercero = "";

  // Consulta que trae los datos del tercero
          $queryTercero = "SELECT t.razonsocial, CONCAT(', ',s.nombre) sucursal,
                                CONCAT(t.nombreuno,' ',t.nombredos,' ', t.apellidouno,' ' ,t.apellidodos) nombre, 
                                  CONCAT( ti.nombre, ': ', t.numeroidentificacion) identificacion 
                           FROM gf_tercero t 
                           LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
                           LEFT JOIN gf_sucursal s ON t.sucursal = s.id_unico
                           WHERE t.id_unico ='$id'
                           AND t.compania = '$compania'";

  $bus = $mysqli->query($queryTercero);
  $datosTercero = mysqli_fetch_row($bus);
  //$datosTercero= $busq[0].'('.$busq[1].')';

  //TIPO DIRECCION
  $Tdir = "SELECT id_unico, nombre FROM gf_tipo_direccion ORDER BY Nombre ASC";
  $Tdire = $mysqli->query($Tdir);
  
  // CIUDAD
  $c= "SELECT c.id_unico, c.nombre, d.nombre 
       FROM gf_ciudad c 
       LEFT JOIN gf_departamento d ON c.departamento = d.id_unico 
       ORDER BY c.nombre ASC";
  $ciu= $mysqli->query($c);
  
  //DATOS TABLA
  $dir = "SELECT d.id_unico, d.direccion, td.Nombre, c.nombre, dep.nombre, d.tipo_direccion, d.ciudad_direccion, d.tercero
          FROM gf_direccion d LEFT JOIN gf_tipo_direccion td ON d.tipo_direccion = td.Id_Unico
          LEFT JOIN gf_ciudad c ON d.ciudad_direccion = c.id_unico
          JOIN gf_departamento dep ON dep.id_unico= c.departamento
          WHERE d.tercero = '$id'";
  $direccion = $mysqli->query($dir);
  ?>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.js" rel="stylesheet">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>


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
label#direccion-error, #tipodireccion-error, #ciudad-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}
</style>
<title>Registrar Dirección</title>

</head>
<body>
    <div class="container-fluid text-center">	
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Registrar Dirección</h2>
                <a href="DatosBasicos.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>

                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">
                    <?php
                        if ($datosTercero[0] != NULL){
                            echo $datosTercero[0] . ' ' . $datosTercero[1] . ' (' . $datosTercero[3] . ')';                    // Razon social (tipo y numero de identificacion)
                        }else{
                            echo $datosTercero[2] . ' (' . $datosTercero[3] . ')';                    // Nombre1 y apellido 1 (tipo y numero de identificacion)
                        }       
                    ?>               
                </h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">				 	
                    <form name="form" id="form" method="POST" class="form-inline" enctype="multipart/form-data" action="json/registrar_GF_DIRECCION_TERCEROJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top:10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="tercero" value="<?php echo $id ?>">
                        <div class="form-group form-inline" style="margin-top:-20px; margin-left: -7px; width: 900px">
                            
                            <label for="direccion" class=" control-label col-sm-2" style="width:100px; margin-top:20px" ><strong style="color:#03C1FB;">*</strong>Dirección:</label>
                            <input type="text" name="direccion" id="direccion" style="width:150px; margin-top: 15px;height: 30px" title="Ingrese la dirección" class="form-control col-sm-1" onkeypress="return txtValida(event,'direccion')" maxlength="150" placeholder="Dirección" required="required"/>
                            <label for="tipodireccion" class="control-label col-sm-2" style="width:83px; margin-top: 15px;"><strong style="color:#03C1FB;">*</strong>Tipo Dirección:</label>
                            <select name="tipodireccion" id="tipodireccion" class=" select2_single form-control" title="Seleccione tipo dirección" required="required" style="width:150px; margin-top: 15px;height: 30px;">
                                <option value="">Tipo Dirección</option>
                                <?php 
                                while($rowTd = mysqli_fetch_row($Tdire)){?>
                                <option value="<?php echo $rowTd[0] ?>"><?php echo ucwords((strtolower($rowTd[1])));}?></option>;
                            </select>
                            <input type="hidden" id="ciudad" name="ciudad" required="required" title="Seleccione ciudad">
                            <label for="ciudad" style="margin-left: 19px;"><strong style="color:#03C1FB;">*</strong>Ciudad:</label>
                            <select name="ciudad2" id="ciudad2" class="select2_single form-control" title="Seleccione ciudad" required="required" onchange="llenar();" style="width:150px; margin-top: 15px;height: 30px;">
                                <option value="">Ciudad</option>
                                <?php 
                                while($rowC = mysqli_fetch_row($ciu)){?>
                                <option value="<?php echo $rowC[0] ?>"><?php echo ucwords((strtolower($rowC[1].' - '.$rowC[2])));}?></option>;
                            </select>
                            
                            <button type="submit" class="btn btn-primary sombra" style="margin-left:10px; margin-top: 15px;">Guardar</button>
                           
                           <input type="hidden" name="MM_insert" >
                        </div>      
                    </form>       
                </div>                               
                <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 10px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="oculto">Identificador</td>
                                    <td width="7%"></td>
                                    <td class="cabeza"><strong>Dirección</strong></td>
                                    <td class="cabeza"><strong>Tipo dirección</strong></td>
                                    <td class="cabeza"><strong>Ciudad</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Dirección</th>
                                    <th>Tipo dirección</th>
                                    <th>Ciudad</th>
                                </tr>
                            </thead>
                            <tbody>   
                                <?php
                                while ($row = mysqli_fetch_row($direccion)) { ?>
                                <tr>               
                                    <td style="display: none;"><?php echo $row[0];?></td>
                                    <td align="center" class="campos">
                                        <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a onclick="javascript:modificarModal(<?php echo "'".ucwords(strtolower(($row[1])))."',".$row[5].','.$row[6].','.$row[0].','.$row[7];?>);"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td class="campos"><?php echo ucwords(strtolower(($row[1])));?></td>
                                    <td class="campos"><?php echo ucwords(strtolower($row[2]));?></td>
                                    <td class="campos"><?php echo ucwords(strtolower($row[3].' - '.$row[4]));?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-2 text-center" align="center" style="margin-top:-15px;">
                <h2 class="titulo" align="center" style=" font-size:17px;">Adicional</h2>
                <div  align="center">
                    <a href="registrar_GF_TIPO_DIRECCION.php" class="btn btn-primary btnInfo">TIPO DIRECCIÓN</a>          
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
    
    <script>
  function llenar(){
      var ciudad = document.getElementById('ciudad2').value;
      document.getElementById('ciudad').value= ciudad;
  }
  </script>
  
    <!--- MODIFICAR !-->
    <?php 
    // TIPO DIRECCION
    $Tdire = "SELECT Id_Unico, Nombre FROM gf_tipo_direccion ORDER BY Nombre ASC";
    $Tdirecc = $mysqli->query($Tdire);
    //CIUDAD
    $ci= "SELECT c.id_unico, c.nombre, d.nombre FROM gf_ciudad c LEFT JOIN gf_departamento d ON c.departamento = d.id_unico ORDER BY c.nombre ASC";
    $ciud= $mysqli->query($ci);
    ?>
    <div class="modal fade" id="myModalUpdate" role="dialog" align="center" >
        <div class="modal-dialog">
            
            <form  name="form" method="POST" action="javascript:modificarItem()">
                <input type="hidden" name="idm" id="idm">
                <input type="hidden" name="tercerom" id="tercerom">
                <div class="modal-content client-form1">
                    <div id="forma-modal" class="modal-header">       
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
                    </div>
                    <div class="modal-body "  align="center">
                        <div class="form-group" align="left">
                            <label  style="margin-left:160px; display:inline-block;"><strong style="color:#03C1FB;">*</strong>Dirección:</label>
                            <input style="display:inline-block; width:250px; font-size: 0.9em; height: 30px;" type="text" name="direccionM" id="direccionM" title="Ingrese la dirección" class="form-control" onkeypress="return txtValida(event,'direccion')" maxlength="150" placeholder="Dirección" style=" height: 55px;width:250px;"  required>
                         </div>
                       <div class="form-group"  style="margin-top: 13px;">
                            <label align="right" style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Tipo Dirección:</label>
                            <select style="display:inline-block; width:250px; padding: 5px;  height:32px; font-size:0.9em;" name="tipod" id="tipod" class="select2_single form-control" title="Seleccione tipo dirección" required>
                                <?php while ($m = mysqli_fetch_row($Tdirecc)) { ?>
                                      <option value="<?php echo $m[0]; ?>">
                                        <?php echo ucwords((strtolower($m[1]))); ?>
                                      </option>
                                <?php  

                                 } ?>
                            </select>                                
                        </div>
                        <div class="form-group"  style="margin-top: 13px;">
                            <label align="right" style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Ciudad:</label>
                            <select style="display:inline-block; width:250px; margin-bottom:15px;  text-align-last:left;" name="ciudadm" id="ciudadm" class="select2_single form-control" title="Seleccione ciudad" required>
                                <?php while ($c = mysqli_fetch_row($ciud)) { ?>
                                      <option value="<?php echo $c[0]; ?>">
                                        <?php echo ucwords((strtolower($c[1].' - '.$c[2]))); ?>
                                      </option>
                                <?php  

                                 } ?>
                            </select>                                
                        </div>
                        <input type="hidden" id="id" name="id">  
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
                        <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
                    </div>
                </div>
            </form>
        </div>
    </div>
<!--  MODAL para los mensajes del  modificar -->
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
<!--  Mensajes de la opción  eliminar -->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">      
                    <p>¿Desea eliminar el registro seleccionado de Dirección?</p>
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
                    <p>Información eliminada correctamente.</p>
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
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizada por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
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
    <script type="text/javascript" src="js/menu.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <!-- MODIFICAR!-->
   <script type="text/javascript">
       
  function modificarModal(direccion, tipod, ciudad, id, tercero){
            document.getElementById('direccionM').value = direccion;
             $("#tipod").val(tipod);
             $("#ciudadm").val(ciudad);
             document.getElementById('idm').value = id;
             document.getElementById('tercerom').value = tercero;
            
            
      $("#myModalUpdate").modal('show');
  }
  function modificarItem()
    {
      var result = '';
      var id= document.getElementById('idm').value; 
      var dir= ('"'+document.getElementById('direccionM').value+'"');
      var direccion1 = encodeURIComponent(dir);
      var tipod=document.getElementById('tipod').value;
      var ciudad= document.getElementById('ciudadm').value;
      var tercero=document.getElementById('tercerom').value;
      $.ajax({
        type:"GET",
       url:"json/modificar_GF_DIRECCION_TERCEROJson.php?id="+id+"&tipo="+tipod+"&ciudad="+ciudad+"&tercero="+tercero+"&direccion1="+direccion1,
        success: function (data) {
          result = JSON.parse(data);
          if(result==true){
            $("#myModalUpdate").modal('hide');
            $("#myModal5").modal('show');
            $("#ver5").click(function(){
                
              $("#myModal5").modal('hide');
              window.location.reload();
            });
          }else{
            $("#myModal6").modal('show');
            $("#ver6").click(function(){
              $("#myModal6").modal('hide');
            });
          }
        }
      });
    }

</script>

<!-- Funci�n para la opcion eliminar -->

    <script type="text/javascript">
      function eliminar(id)
      {
       var result = '';
       $("#myModal").modal('show');
       $("#ver").click(function(){
        $("#myModal").modal('hide');
        $.ajax({
          type:"GET",
          url:"json/eliminar_GF_DIRECCION_TERCEROJson.php?id="+id,
          success: function (data) {
            result = JSON.parse(data);
            if(result==true)
              $("#myModal1").modal('show');
            else
              $("#myModal2").modal('show');
          }
        });
      });
      $("#ver1").click(function(){
           document.location = "GF_DIRECCION_DB.php";
         });
        $("#ver2").click(function(){
           document.location = "GF_DIRECCION_DB.php";
         });
     }
    </script>
</body>
</html>					