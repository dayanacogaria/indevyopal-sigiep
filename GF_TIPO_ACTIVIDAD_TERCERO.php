<?php 
  //llamado a la clase de conexion
  require_once 'Conexion/conexion.php';
  session_start();

  $datosTercero = "";
  $id = $_SESSION['id_tercero'];

  if($_SESSION['perfil'] == "N"){
    //Consulta para el listado de registro de la tabla gf_tercero para naturales.
    $queryTercero = "SELECT t.NombreUno,t.NombreDos, t.ApellidoUno, t.ApellidoDos, CONCAT('(', ti.Nombre, ': ', t.NumeroIdentificacion, ')') identificacion 
      FROM gf_tercero t 
      LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico 
      WHERE t.Id_Unico =$id";
    }
      elseif($_SESSION['perfil'] == "J")
    {
      //Consulta para el listado de registro de la tabla gf_tercero para jurídicos.
      $queryTercero = "SELECT t.razonsocial, CONCAT(', ',s.nombre) sucursal, CONCAT('(', ti.Nombre, ': ', t.NumeroIdentificacion, ')') identificacion 
      FROM gf_tercero t
      LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
      LEFT JOIN gf_sucursal s ON t.sucursal = s.id_unico 
      WHERE t.Id_Unico = $id";
    }

  $tercero = $mysqli->query($queryTercero);
  $rowTer = mysqli_fetch_row($tercero);
    foreach ($rowTer as $i)
    {
      $datosTercero .= $i." ";
    }


  // llamado al encanbezado del listar
  require_once 'head_listar.php';

 ?>

<title>Tipo Actividad Tercero</title>
</head>
<body>
	<div class="container-fluid text-center">	
		<div class="row content">

<!--Lllamado al menu    --> 				
			<?php require_once 'menu.php'; ?>
			<div class="col-sm-8 text-left">
				 <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Tipo Actividad Tercero</h2>
<!-- Botón volver -->          
           <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
<!-- Nombre del tercero -->
				  <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords(      (strtolower($datosTercero))); 
          ?></h4>
<!-- Caja para REGISTRAR la informacion -->
			 <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">				 	
				 	<form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarTipoActividadTerJson.php">
  				 		<p align="center" style="margin-bottom: 25px; margin-top:5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

              <input type="hidden" name="tercero" value="<?php echo $id ?>">

<!-- Combo TIPO ACTIVIDAD -->
<div class="form-group" class="col-sm-4" style="margin-top:-30px;" align="center">
    				 			<label for="tipoA" class="control-label"><strong style="color:#03C1FB;">*</strong>Tipo Actividad:</label>
    				 			<select style="display:inline-block" name="tipoA" id="tipoA" class="form-control" title="Seleccione el tipo actividad" onblur="return existente()" required>
    				 				<option value="">Tipo Actividad</option>
   				 				<?php

                  //consulta para trear los datos del combo tipo actividad
                     $tipoA = "SELECT id_unico, nombre, codigo_actividad FROM gf_tipo_actividad ORDER BY nombre ASC";
                    $tipoAct = $mysqli->query($tipoA);

                     while ($mt = mysqli_fetch_assoc($tipoAct)) { ?>
    				 					<option value="<?php echo $mt["id_unico"]; ?>">
    				 						<?php echo ucwords((strtolower($mt["nombre"])."   (C&oacute;digo: ".($mt["codigo_actividad"]).")")); ?>
    				 					</option>
    				 				<?php  } ?>
    				 			</select>
                    <input type="hidden" name="tercero" value="<?php echo $id ?>">
                 <button type="submit" class="btn btn-primary sombra" style="margin-left:10px; margin-top: 10px;">Guardar</button>
            </div>			 		

  				 		<div align="center"></div>
         			<div class="texto" style="display:none"></div>
         			<input type="hidden" name="MM_insert" >
				 	</form>       
			 </div>



<!--  tabla para LISTAR la informacion -->                                   
       <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
              
              <thead>
                <tr>
                <td class="oculto">Identificador</td>
                <td width="7%"></td>
                <td class="cabeza"><strong>Tipo Actividad</strong></td>
                </tr>

                <tr>
                <th class="oculto">Identificador</th>
                <th width="7%"></th>
                <th>Tipo Actividad</th>
              </thead>

              <tbody>   
                <?php 

                //consulta para traer los datos a listar
                  $tipoA2 = "SELECT A.Id_Unico, A.Nombre, B.tipoactividad, A.codigo_actividad  
                        FROM gf_tipo_actividad A 
                        LEFT JOIN gf_tipo_actividad_tercero B ON B.tipoactividad = A.id_unico 
                        WHERE B.Tercero = '$id'";
                  $tipoAct2 = $mysqli->query($tipoA2);

                while ($row = mysqli_fetch_row($tipoAct2)) { ?>
                  
                  <tr>               
                    <td style="display: none;"><?php echo $row[0]?></td>
                    <td align="center"  class="campos">
                      <a href="#" onclick="javascript:eliminarItem(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                      <a onclick="modificarModal(<?php echo $id;?>,<?php echo $row[2];?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                    </td>
                    <td class="campos"><?php echo ($row[1])."  (C&oacute;digo: ".($row[3]).")";?></td>
                  </tr>
                <?php
                }
                 ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
			
    			
<!--  Botones opcionales del lado derecho  -->
			<div class="col-sm-2 text-center" align="center">
  			<h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Adicional</h2>
          <div  align="center">
				    <a href="GF_TIPO_ACTIVIDAD.php" class="btn btn-primary sombra" style="margin-left:10px; margin-top:5px">TIPO ACTIVIDAD</a>

          </div>
      </div>
  </div>
</div>

<!--  LLamado al pie de pagina -->	
	<?php require_once 'footer.php'; ?>

<!--  MODAL y opcion  MODIFICAR  informacion  -->  
<div class="modal fade" id="myModalUpdate" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content client-form1">
      <div id="forma-modal" class="modal-header">       
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
      </div>

      <div class="modal-body ">
        <form  name="form" method="POST" action="javascript:modificarItem()">
          <div style="margin-top: 13px;">
            <label style="display:inline-block; width:140px">Tipo Actividad:</label>
            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="tipoActmodal" id="tipoActmodal" class="form-control" title="Seleccione tipo actividad" required>
                <?php 

                //consultas para modificar el campo TIPO ACTIVIDAD
                  $tipoA3 = "SELECT id_unico, nombre, codigo_actividad FROM gf_tipo_actividad ORDER BY nombre ASC";
                  $tipoAct3 = $mysqli->query($tipoA3);


                while ($mc = mysqli_fetch_row($tipoAct3)) { ?>
                  <option value="<?php echo $mc[0]; ?>">
                    <?php echo ucwords((strtolower($mc[1])."   (C&oacute;digo: ".($mc[2]).")")); ?>
                  </option>
                <?php  

                 } ?>
            </select>          
              <input type="hidden" id="id" name="id">           
          </div>
      </div>
      <div id="forma-modal" class="modal-footer">
          <button type="submit" class="btn" style="color: #000; margin-top: 2px">Modificar</button>
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
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Informaci&oacute;n modificada correctamente.</p>
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

<!--  MODAL para los mensajes de la opcion  eliminar -->

   <div class="modal fade" id="myModal" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">      
        <p>¿Desea eliminar el registro seleccionado de Tipo Actividad Tercero?</p>
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
        <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>


<!-- librerias -->
 	<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

<!-- Función para retornar al formulario principal. -->
<script type="text/javascript">

  $("#ver5").click(function(){
   
   document.location = "GF_TIPO_ACTIVIDAD_TERCERO.php?id=<?php echo $id ?>";
 });

$("#ver1").click(function(){
   
   document.location = "GF_TIPO_ACTIVIDAD_TERCERO.php?id=<?php echo $id ?>";
 });

$("#ver2").click(function(){
   
   document.location = "GF_TIPO_ACTIVIDAD_TERCERO.php?id=<?php echo $id ?>";
 });

</script>

<!-- Función para la opcion modificar. -->

   <script type="text/javascript">
  function modificarModal(id,tipoA){
    
    $("#tipoActmodal").val(tipoA);
    document.getElementById('id').value = id;
      $("#myModalUpdate").modal('show');
  }
  function modificarItem()
    {
      var result = '';
      var id= document.getElementById('id').value; 
      var tipoActi= document.getElementById('tipoActmodal').value;
      $.ajax({
        type:"GET",
        url:"json/modificarTipoActividadTerJson.php?p1="+id+"&p2="+tipoActi,
        success: function (data) {
          result = JSON.parse(data);
          if(result==true){
            $("#myModal5").modal('show');
            $("#ver5").click(function(){
              $("#myModal5").modal('hide');
              
            });
          }else{
            $("#myModal6").modal('show');
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
      url:"json/eliminarTipoActividadTer.php?id="+id,
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