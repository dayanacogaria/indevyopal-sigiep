<?php 
  //llamado a la clase de conexion
  require_once 'Conexion/conexion.php';
  session_start();

  $id = $_SESSION['id_tercero'];

//consulta para traer los datos del tercero
  $queryTercero = "SELECT t.id_unico, t.nombreuno, t.nombredos, t.apellidouno, t.apellidoDos, t.numeroidentificacion, ti.nombre 
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion=ti.id_unico
            WHERE t.id_unico =$id";
$ter="SELECT  CONCAT(nombreuno,' ',nombredos,' ',apellidouno,' ',apellidodos) AS 'NOMBRE', "
        . "id_unico, numeroidentificacion FROM gf_tercero WHERE id_unico = $id";
$tercero = $mysqli->query($ter);
$rowTerc = mysqli_fetch_row($tercero);


  // llamado al encanbezado del listar
  require_once 'head_listar.php';

 ?>

<title>Cargo Tercero</title>
</head>
<body>
	<div class="container-fluid text-center">	
		<div class="row content">

<!--Lllamado al menu    --> 				
			<?php require_once 'menu.php'; ?>
			<div class="col-sm-8 text-left" style="margin-top:-5px">
				 <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Cargo Tercero</h2>
<!-- Botón volver -->          
           <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
<!-- Nombre del tercero -->
				  <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords(strtolower($rowTerc[0].'('.$rowTerc[2].')'))?></h5>
<!-- Caja para REGISTRAR la informacion -->
			 <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top:5px;" class="client-form">				 	
				 	<form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarCargoTerceroJson.php">
  				 		<p align="center" style="margin-bottom: 25px; margin-top:5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

              <input type="hidden" name="tercero" value="<?php echo $id ?>">

<!-- Combo Cargo -->
			 		<div class="form-group" class="col-sm-4" style="margin-top:-30px;" align="center">
    				 			<label for="cargo" class="control-label"><strong style="color:#03C1FB;">*</strong>Cargo:</label>
    				 			<select style="display:inline-block" name="cargo" id="cargo" class="form-control" title="Seleccione el cargo" onblur="return existente()" required>
    				 				<option value="">Cargo</option>
    				 				<?php 

                    //consulta para trear los datos del combo de cargo
                      $tipoA = "SELECT id_unico, nombre, numero_plazas FROM gf_cargo ORDER BY nombre ASC";
                      $tipoAct = $mysqli->query($tipoA);

                      while ($mc = mysqli_fetch_assoc($tipoAct)) { ?>
                        <option value="<?php echo $mc["id_unico"]; ?>">
      				 						<?php echo ucwords(     (strtolower($mc["nombre"])."   (No.Plazas:".($mc["numero_plazas"]).")")); ?>
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
       <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 5px; margin-bottom: 5px;">          
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
              
              <thead>
                <tr>
                <td class="oculto">Identificador</td>
                <td width="7%"></td>
                <td class="cabeza"><strong>Cargo</strong></td>
                </tr>

                <tr>
                <th class="oculto">Identificador</th>
                <th width="7%"></th>
                <th>Cargo</th>
                </tr>
              </thead>

              <tbody>   
                <?php

                //consulta para traer los datos a listar
                  $tipoA2 = "SELECT A.id_unico, A.nombre, B.cargo, A.numero_plazas
                      FROM gf_cargo A
                      LEFT JOIN gf_cargo_tercero B ON B.cargo = A.id_unico
                      WHERE B.tercero = $id";
                  $tipoAct2 = $mysqli->query($tipoA2);

                while ($rowCt = mysqli_fetch_row($tipoAct2)) { ?>
                  
                  <tr>               
                    <td style="display: none;"><?php echo $rowCt[0]?></td>
                    <td align="center" class="campos">
                      <a href="#" onclick="javascript:eliminarItem(<?php echo $rowCt[0].','.$id?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                      <a onclick="modificarModal(<?php echo $id;?>,<?php echo $rowCt[2];?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                    </td>
                    <td class="campos"><?php echo ($rowCt[1])."  (No.Plazas: ".($rowCt[3]).")";?></td>
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
      <div class="col-sm-2 text-center" align="center" style="margin-top:-20px">
          <h2 class="titulo" align="center" style=" font-size:17px;">Adicional</h2>
          <div  align="center">
            <a href="registrar_GF_CARGO.php" class="btn btn-primary btnInfo">CARGO</a>          
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
            <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Cargo:</label>
            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="tipoActmodal" id="tipoActmodal" class="form-control" title="Seleccione el cargo" required>
                <?php 

                //consultas para modificar el campo cargo
                  $tipoA3 = "SELECT id_unico, nombre, numero_plazas FROM gf_cargo ORDER BY nombre ASC";
                  $tipoAct3 = $mysqli->query($tipoA3);

                while ($mc = mysqli_fetch_row($tipoAct3)) { ?>
                  <option value="<?php echo $mc[0]; ?>">
                    <?php echo ucwords(     (strtolower($mc[1])."   (No.Plazas:".($mc[2]).")")); ?>
                  </option>
                <?php  

                 } ?>
            </select>          
              <input type="hidden" id="id" name="id">       
              <input type="hidden" id="cargoA" name="cargoA">      
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
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La informaci&oacute;n no se ha podido modificar.</p>
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
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
        <p>Las plazas de este cargo ya estan ocupadas.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver7" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal8" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
        <p>El cargo ya ha sido asignado a esta persona.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver8" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
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
        <p> &iquest;Desea eliminar el registro seleccionado de Cargo Tercero?</p>
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
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        
          <p>Informaci&oacute;n eliminada correctamente.</p>

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
        <p>No se pudo eliminar la informaci&oacute;n, el registro seleccionado está siendo utilizado por otra dependencia.</p>
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
   
   document.location = "GF_CARGO_TERCERO.php?id=<?php echo $id ?>";
 });

$("#ver1").click(function(){
   
   document.location = "GF_CARGO_TERCERO.php?id=<?php echo $id ?>";
 });

$("#ver2").click(function(){
   
   document.location = "GF_CARGO_TERCERO.php?id=<?php echo $id ?>";
 });

</script>

<!-- Función para la opcion modificar. -->

   <script type="text/javascript">
  function modificarModal(id,tipoA){
    
    $("#tipoActmodal").val(tipoA);
    $("#cargoA").val(tipoA);
    document.getElementById('id').value = id;
      $("#myModalUpdate").modal('show');
  }
  function modificarItem()
    {
      var result = '';
      var id= document.getElementById('id').value; 
      var tipoActi= document.getElementById('tipoActmodal').value;
      var cargoAn= document.getElementById('cargoA').value;
      $.ajax({
        type:"GET",
        url:"json/modificarCargoTerceroJson.php?p1="+id+"&p2="+tipoActi+"&cargoAn="+cargoAn,
        success: function (data) {
          result = JSON.parse(data);
          if(result=='1'){
            $("#myModal5").modal('show');
            $("#ver5").click(function(){
              $("#myModal5").modal('hide');
              $("#myModalUpdate").modal('hide');
            });
          }else{
            if(result=='2'){
              $("#myModal8").modal('show');
            $("#ver8").click(function(){
              $("#myModal8").modal('hide');
              $("#myModalUpdate").modal('hide');
            });

            }else {
              if(result=='3'){
                $("#myModal7").modal('show');
            $("#ver7").click(function(){
              $("#myModal7").modal('hide');
              $("#myModalUpdate").modal('hide');
            });

              }else {
                $("#myModal6").modal('show');
            $("#ver6").click(function(){
              $("#myModal6").modal('hide');
              $("#myModalUpdate").modal('hide');
            });

              }
            }
          }
        }
      });
    }

</script>

<!-- Función para la opcion eliminar -->

<script type="text/javascript">
  function eliminarItem(id, tercero)
  {
   var result = '';
   $("#myModal").modal('show');
   $("#ver").click(function(){
    $("#myModal").modal('hide');
    $.ajax({
      type:"GET",
      url:"json/eliminarCargoTercero.php?id="+id+"&tercero="+tercero,
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