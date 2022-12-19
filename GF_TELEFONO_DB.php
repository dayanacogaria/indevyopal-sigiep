<?php 
  //llamado a la clase de conexion
  require_once 'Conexion/conexion.php';
  require_once 'head_listar.php';
$datosTercero = "";
$compania   = $_SESSION['compania'];
$id_usuario = $_SESSION['id_usuario'];
$id = $_SESSION['usuario_tercero'];

 // Consulta para llamar el perfil del usuario
  $perfilU = "SELECT pf.perfil, gu.id_unico, t.razonsocial
              FROM gf_perfil p, gf_perfil_tercero pt, gs_usuario gu, gf_tercero t
              WHERE p.id_unico = pf.perfil
              AND p.tercero = pf.id_unico
              AND p.tercero = '$id'
              AND gu.id_unico = '$id_usuario'
              AND gu.id_unico = t.tercero";
  $resultadoPerfil = $mysqli->query($perfilU);
  
  // PERFIL CONSULTADO
  $perfil = $resultadoPerfil[0];

  $razonsocial = $resultadoPerfil[2];
  //tercero 
  // perfil de tercero Natural
         $queryTercero = "SELECT t.razonsocial, CONCAT(', ',s.nombre) sucursal,
                                CONCAT(t.nombreuno,' ',t.nombredos,' ', t.apellidouno,' ' ,t.apellidodos) nombre, 
                                  CONCAT( ti.nombre, ': ', t.numeroidentificacion) identificacion 
                           FROM gf_tercero t 
                           LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
                           LEFT JOIN gf_sucursal s ON t.sucursal = s.id_unico
                           WHERE t.id_unico ='$id'
                           AND t.compania = '$compania'";
  $tercero = $mysqli->query($queryTercero);
  $rowTer = mysqli_fetch_row($tercero);
    
    foreach ($rowTer as $i)
    {
      $datosTercero .= $i." ";
    }

    // llamado al encabezado del listar

 ?>
 <!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
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
label#valor-error, #tel-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}
</style>
<title>Registrar Tel&eacute;fono</title>
</head>
<body>
	<div class="container-fluid text-center">	
		<div class="row content">

<!--Lllamado al menu    --> 				
			<?php require_once 'menu.php'; ?>
			<div class="col-sm-8 text-left">
				 <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Registrar Telefono</h2>
<!-- Botón volver -->          
        <a href="DatosBasicos.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
<!-- Nombre del tercero -->
        <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">
                          <?php
                        if ($rowTer[0] != NULL){
                            echo $rowTer[0] . ' ' . $rowTer[1] . ' (' . $rowTer[3] . ')';   // Razon social, sucursa, y tipo y numero de identificacion
                        }else{
                            echo $rowTer[2] . ' (' . $rowTer[3] . ')';                    // Nombre1 y apellido1 (tipo y numero de identificacion)
                        }       
                    ?>               
                </h5>
<!-- Caja para REGISTRAR la informacion -->
       <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form contenedorForma col-sm-12"> 

          <form name="form" id="form" method="POST" class="form-horizontal" enctype="multipart/form-data" action="json/registrarTelefonoJson.php">

              <p align="center" style="margin-bottom: 10px; margin-top:10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

              <input type="hidden" name="tercero" value="<?php echo $id;?>">

<!-- Combo TIPO TELEFONO -->
      <div  class="form-group form-inline col-sm-12" style=" margin-bottom: 0px; padding: 0px;">

        <!--    -->

            <div class="col-sm-3" align="right">
              <label for="tel" class="control-label"><strong style="color:#03C1FB;">*</strong>Tipo Tel&eacute;fono:</label>
            </div>
            
          <div class="col-sm-3">
            <select name="tel" id="tel" class="select2_single form-control " title="Seleccione el tipo tel&eacute;fono" required style="width:180px">
                <option value="">Tipo tel&eacute;fono</option>
                <?php 

              //consulta para trear los datos del combo de perfil_condicion
                $tipoA = "SELECT id_unico, nombre FROM gf_tipo_telefono ORDER BY nombre ASC";
                $tipoAct = $mysqli->query($tipoA);

                while($rowC = mysqli_fetch_assoc($tipoAct)){?>
                <option value="<?php echo $rowC['id_unico'] ?>"><?php echo ucwords((strtolower($rowC['nombre'])));}?></option>;
              </select> 
         
            </div>
              
                  
<!-- campo VALOR-->                  
     
     <div class="col-sm-1" align="right">
      <label for="valor" class="control-label" style="margin-right: 0px; margin-top: 0px;"><strong style="color:#03C1FB;">*</strong>Valor:</label>
     </div>
                  
    <div class="col-sm-3">
        <input type="text" name="valor" id="valor" class="form-control" maxlength="20" title="Ingrese el valor" onkeypress="return txtValida(event,'num')" placeholder="Valor" style="width:180px" required>
    </div>
                  
            
            <!--   
             <input type="hidden" name="tercero" value="<?php //echo $id ?>">-->
             <div class="col-sm-1">
              <button type="submit" class="btn btn-primary sombra" style="margin-left: 5px; margin-top: 0px;">Guardar</button>        
             </div>
             </div>          

              <div align="center"></div>
              <div class="texto" style="display:none"></div>
              <input type="hidden" name="MM_insert" >
          </form>       
  </div>

<!--  tabla para LISTAR la informacion -->                                   
       <div align="center" class="table-responsive col-sm-12" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
              
              <thead>
                <tr>
                <td class="oculto">Identificador</td>
                <td width="7%"></td>
                <td class="cabeza"><strong>Tipo Tel&eacute;fono</strong></td>
                <td class="cabeza"><strong>Valor</strong></td>
                </tr>

                <tr>
                <th class="oculto">Identificador</th>
                <th width="7%"></th>
                <th>Tipo Tel&eacute;fono</th>
                <th>Valor</th>
                </tr>
              </thead>

              <tbody>   
                <?php

              //consulta para traer los datos a listar
                $tipoA2 = "SELECT t.id_unico,t.tipo_telefono,t.valor,tt.nombre 
                          FROM gf_telefono t 
                          LEFT JOIN  gf_tipo_telefono tt ON  t.tipo_telefono = tt.id_unico
                          WHERE t.tercero = $id";
                $tipoAct2 = $mysqli->query($tipoA2);


                while ($row = mysqli_fetch_row($tipoAct2)) { ?>
                  
                  <tr>               
                    <td style="display: none;"><?php echo $row[0]?></td>
                    <td align="center" class="campos">
                      <a href="#" onclick="javascript:eliminarItem(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                      <a onclick="modificarModal(<?php echo $id;?>,<?php echo $row[0];?>,'<?php echo ($row[2])?>','<?php echo ($row[1])?>');"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                    </td>
                    <td class="campos"><?php echo($row[3])?></td>
                    <td class="campos"><?php echo($row[2])?></td>
                  </tr>
                <?php
                }
                 ?>
              </tbody>
            </table>
          </div>
		  </div>
		  
         </div> <!-- Cierra col-sm-8 text-left -->
     
			
    			
<!--  Botones opcionales del lado derecho  -->
			<div class="col-sm-2 text-center" align="center">
				<h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Adicional</h2>
			
				<div  align="center">
					<a href="registrar_GF_TIPO_TELEFONO.php" class="btn btn-primary sombra" style="margin-left:10px; margin-top:5px">TIPO TELEFONO</a>
				</div>
			</div>
		  
     
  </div> <!-- Cierra row content -->
</div> <!-- Cierra container-fluid text-center --> 

<!--  LLamado al pie de pagina -->	
	<?php require_once 'footer.php'; ?>

<!--  MODAL y opcion  MODIFICAR  informacion  -->  
<div class="modal fade" id="myModalUpdate" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content client-form1">
      <div id="forma-modal" class="modal-header">       
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
      </div>

<!-- Consulta para modificar el combo TIPO TELEFONO   -->
      <?php 
      $tipoA3 = "SELECT id_unico, nombre FROM gf_tipo_telefono ORDER BY nombre ASC";
      $tipoAct3 = $mysqli->query($tipoA3);
       ?>

      <div class="modal-body ">
        <form id="frm2" name="frm2" method="POST" action="javascript:modificarItem()">
          <input type="hidden" id="tipoteledi" name="tipoteledi">
          <input type="hidden" id="valorAx" name="valorAx">
          <div class="form-group" style="margin-top: 13px;">
            <label style="display:inline-block; width:137px; padding-left: 27px;"><strong style="color:#03C1FB;">*</strong>Tipo Tel&eacute;fono:</label>
            <select style="display:inline-block; width:252px; margin-bottom:15px; height:40px" name="tipoActmodal" id="tipoActmodal" class="select2_single " title="Seleccione Tipo Tel&eacute;fono" required="required">
                <?php while ($modTel = mysqli_fetch_row($tipoAct3)) { ?>
                      <option value="<?php echo $modTel[0]; ?>">
                        <?php
                        echo ucwords((strtolower($modTel[1]))); 
                         ?>
                      </option>
                <?php  

                 } ?>
            </select>                                
          </div>

        

          <div class="form-group" >

          <table style="margin-right: 10px;">
          <tr>
            <td align="right">
              
                 <label for="valor"  style=" width:140px; margin-right: 16px; margin-bottom: 30px;  margin-left: -6px;" ><strong style="color:#03C1FB;">*</strong>Valor:</label></td>
              <td>
                <input type="text" name="valorA" id="valorA" class="form-control" maxlength="20" title="Ingrese el valor" onkeypress="return txtValida(event,'num')" placeholder="Valor" style="width:252px" required>
              </td>

          </tr>
            
          </table>

           

          </div>

           <input type="hidden" id="id" name="id">  
      </div>


      <div id="forma-modal" class="modal-footer">
          <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
        <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
      </div>
      </form>
    </div>
  </div>
</div>



<!--  MODAL para los mensajes del  modificar -->

<div class="modal fade" id="myModal5" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacuten</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Informaci&oacuten modificada correctamente.</p>
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
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacuten</h4>
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

<!--  MODAL para los mensajes de la opcion  eliminar -->

   <div class="modal fade" id="myModal" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">      
        <p>¿Desea eliminar el registro seleccionado de Tel&eacute;fono?</p>
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
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No se pudo eliminar la información, el registro seleccionado est&aacute siendo utilizado por otra dependencia.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myModalrd" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>El valor ya  existe.</p>
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

<!-- librerias -->
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
<!-- Función para retornar al formulario principal. -->
<script type="text/javascript">

  $("#ver5").click(function(){
   
   document.location = "GF_TELEFONO_DB.php?id=<?php echo $id ?>";
 });

$("#ver1").click(function(){
   
   document.location = "GF_TELEFONO_DB.php?id=<?php echo $id ?>";
 });

$("#ver2").click(function(){
   
   document.location = "GF_TELEFONO_DB.php?id=<?php echo $id ?>";
 });

</script>

<!-- Función para la opcion modificar. -->

   <script type="text/javascript">
  function modificarModal(id,tipoA,valor,tipotel){
    
    $("#tipoActmodal").val(tipotel);
    $("#tipoteledi").val(tipoA);
    document.getElementById('id').value = id;
    document.getElementById('valorA').value = valor;
    document.getElementById('valorAx').value = valor;
      $("#myModalUpdate").modal('show');
  }
  function modificarItem()
    {
      var result = '';
      var id= document.getElementById('tipoteledi').value;
      var tipoActi= document.getElementById('tipoActmodal').value;
      var val=document.getElementById('valorA').value;
      var valx=document.getElementById('valorAx').value;
      console.log(valor);
      $.ajax({
        type:"GET",
        url:"json/modificarTelefono.php?p1="+id+"&p2="+tipoActi+"&p3="+val+'&p4='+valx,
        success: function (data) {
          result = JSON.parse(data);
          if(result==1){
            $("#myModalUpdate").modal('hide');
            $("#myModal5").modal('show');
            $("#ver5").click(function(){
            $("#myModal5").modal('hide');              
            });
          }else if (result ==0){
            $("#myModal6").modal('show');
          }else if (result ==2){
            $("#myModalrd").modal('show');
          }
        }
      });
    }

</script>

<!-- Función para la opcion eliminar -->

<script type="text/javascript">
  function eliminarItem(id)
  {
   var result = '';
   $("#myModal").modal('show');
   $("#ver").click(function(){
    $("#myModal").modal('hide');
    $.ajax({
      type:"GET",
      url:"json/eliminarTelefono.php?id="+id,
      success: function (data) {
        result = JSON.parse(data);
        if(result==true)
          $("#myModal1").modal('show');
        else
          $("#myModal2").modal('show');
      }
    });
  });
 }
</script>
</body>
</html>					