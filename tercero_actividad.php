<?php 
	
	require_once 'Conexion/conexion.php';
	session_start();

	require_once 'head_listar.php';

	$tipoA = "SELECT Id_Unico, Nombre  FROM gf_tipo_actividad ORDER BY Nombre ASC";
	$tipoAct = $mysqli->query($tipoA);

  $id = $_GET['id'];

  $queryTercero = "SELECT T.Id_Unico, T.NombreUno, T.NombreDos, T.ApellidoUno, T.ApellidoDos, T.NumeroIdentificacion, TD.Nombre FROM gf_tercero T, gf_tipo_identificacion TD WHERE T.TipoIdentificacion = TD.Id_Unico AND T.Id_Unico = '$id'";

    $tercero = $mysqli -> query($queryTercero);

    $rowTercero = mysqli_fetch_row($tercero);

    $n1 = $rowTercero[1];
    $n2 = $rowTercero[2];
    $a1 = $rowTercero[3];
    $a2 = $rowTercero[4];
    $td = $rowTercero[6];
    $nd = $rowTercero[5];

    $tipoA2 = "SELECT B.Id_Unico, A.Nombre, B.TipoActividad  FROM gf_tipo_actividad A, gf_tipo_actividad_tercero B
    WHERE B.TipoActividad = A.Id_Unico 
    AND B.Tercero = '$id'";
  $tipoAct2 = $mysqli->query($tipoA2);


  $tipoA3 = "SELECT Nombre, Id_Unico  FROM gf_tipo_actividad
    ";
  $tipoAct3 = $mysqli->query($tipoA3);



 ?>
<title>Actividad Tercero</title>
</head>
<body>

	<div class="container-fluid text-center">
		
		<div class="row content">
				
			<?php require_once 'menu.php'; ?>

			<div class="col-sm-8 text-left">
				
				 <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Actividad Tercero</h2>
                                       
                                 <a href="EDITAR_TERCERO_EMPLEADO_NATURAL2.php?id=<?php echo md5($id) ?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:200%; vertical-align:middle;text-decoration:none"></a>

				 <h4 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;"><?php echo $n1 ?> <?php echo $n2 ?> <?php echo $a1 ?> <?php echo $a2 ?> (<?php echo $td ?>: <?php echo $nd ?>)</h4>

				 <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
				 	
				 	<form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarTerceroActividadJson.php">
				 		
				 		<p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

            <input type="hidden" name="tercero" value="<?php echo $id ?>">

				 		<div class="form-group"  style="margin-top: -10px;">
				 			<label for="tipoAct" class="col-sm-4 control-label">
				 			<strong style="color:#03C1FB;">*</strong>Tipo Actividad:</label>
				 			<select style="display:inline-block" name="tipoAct" id="tipoAct" class="form-control" title="Seleccione tipo actividad" required>
				 				<option value="">Tipo Actividad</option>
				 				<?php while ($ma = mysqli_fetch_assoc($tipoAct)) { ?>
				 					<option value="<?php echo $ma["Id_Unico"]; ?>">
				 						<?php echo ucwords(utf8_encode(strtolower($ma["Nombre"]))); ?>
				 					</option>
				 				<?php  } ?>
				 			</select>

                                                        <button type="submit" class="btn btn-primary sombra" style="margin-left:10px">Guardar</button>

				 		</div>

				 		

				 		<div align="center">

             				

            			</div>

            			<div class="texto" style="display:none"></div>

            			<input type="hidden" name="MM_insert" >

				 	</form>

          

				 </div>
                                   
                                   <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">
          
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
            
            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
              
              <thead>
                
                <tr>

                  <td style="display: none;">Identificador</td>
                  <td width="30px" align="center"></td>
                  <td><strong>Tipo Actividad</strong></td>
                  

                </tr>

                <tr>

                  <th style="display: none;">Identificador</th>
                  <th width="7%"></th>
                  <th>Tipo Actividad</th>
                  

                </tr>

              </thead>
              <tbody>
                
              <?php 
              while ( $row = mysqli_fetch_row($tipoAct2)) { ?>
                
                <tr>
                  
                  <td style="display: none;"><?php echo $row[0]?></td>
                  <td align="center"><a href="#" onclick="javascript:eliminarItem(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a><a onclick="modificarModal(<?php echo $row[0];?>,<?php echo $row[2];?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a></td>
                  <td><?php echo utf8_encode($row[1]);?></td>
                  

                </tr>

              <?php
              }
               ?>

              </tbody>

            </table>

            </div>
            </div>


			</div>

      
			

			<div class="col-sm-2 text-center" align="center">

			<h2 id="forma-titulo3b" align="center" style="margin-top:5px;margin-bottom: 5px; font-size: 20px; height: 30px; margin-right: 4px; margin-left: 4px;">Adicional</h2>

			<div  align="center">
				
				<a href="formTipoActividad.php" class="btn btn-primary sombra" style="margin-left:10px">TIPO ACTIVIDAD</a>

			</div>

		</div>

	</div>

		</div>

		

		

	<?php require_once 'footer.php'; ?>
 
        <div class="modal fade" id="myModalUpdate" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content client-form1">
      <div id="forma-modal" class="modal-header">
        
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
      </div>
      <div class="modal-body ">
        <form  name="form" method="POST" action="javascript:modificarItem()">
          <div style="margin-top: 13px;">
            <label style="display:inline-block; width:140px">Tipo actividad:</label>
            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="tipoActmodal" id="tipoActmodal" class="form-control" title="Seleccione tipo identificación" required>
                <?php while ($ma = mysqli_fetch_row($tipoAct3)) { ?>
                  <option value="<?php echo $ma[1]; ?>">
                    <?php echo ucwords(utf8_encode(strtolower($ma[0]))); ?>
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

<div class="modal fade" id="myModal5" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">InformaciÃ³n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">

            <p>InformaciÃ³n Modificada Correctamente</p>

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
           <p>La Información no se ha podido Modificar</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver6" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
      
        <p>Â¿Desea Eliminar la Actividad Tercero?</p>

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
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">InformaciÃ³n</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        
          <p>Actividad Tercero Eliminada Correctamente</p>

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
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">InformaciÃ³n</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>

	

  	
  
 	<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>

<script type="text/javascript">

  $("#ver5").click(function(){
   
   document.location = "tercero_actividad.php?id=<?php echo $id ?>";
 });

$("#ver1").click(function(){
   
   document.location = "tercero_actividad.php?id=<?php echo $id ?>";
 });

$("#ver2").click(function(){
   
   document.location = "tercero_actividad.php?id=<?php echo $id ?>";
 });

</script>


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
        url:"json/modificarTerceroActividad.php?p1="+id+"&p2="+tipoActi,
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

<script type="text/javascript">
  function eliminarItem(id)
  {
   var result = '';
   $("#myModal").modal('show');
   $("#ver").click(function(){
    $("#myModal").modal('hide');
    $.ajax({
      type:"GET",
      url:"json/eliminarTerceroActividad.php?id="+id,
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