<?php 
######################################################################################################
#*************************************     Modificaciones      **************************************#
######################################################################################################
#03/01/2017 | Erica G. | Parametrizacion Año
######################################################################################################
    require_once 'Conexion/conexion.php';
    require_once 'head_listar.php';
    $anno = $_SESSION['anno'];
  $datosTercero = "";
  if(empty($_GET['id'])){
  $id = $_SESSION['id_tercero'];
  } else {
      $id = $_GET['id'];
  }
 
      //Consulta para el listado de registro de la tabla gf_tercero para jur�dicos.
      $queryTercero = "SELECT IF(CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) 
     IS NULL OR CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) = '',
     (t.razonsocial),
     CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos)) AS NOMBRE, CONCAT(', ',s.nombre) sucursal, CONCAT('(', ti.Nombre, ': ', t.NumeroIdentificacion, ')') identificacion 
      FROM gf_tercero t
      LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
      LEFT JOIN gf_sucursal s ON t.sucursal = s.id_unico 
      WHERE t.Id_Unico = $id ";

  $tercero = $mysqli->query($queryTercero);
  $rowTer = mysqli_fetch_row($tercero);
    foreach ($rowTer as $i)
    {
      $datosTercero .= $i." ";
    }


//consulta para trear los datos del combo Cuenta bancaria
  $tipoA = "SELECT cb.id_unico, cb.numerocuenta, cb.banco, t.id_unico, t.razonsocial 
            FROM gf_cuenta_bancaria cb
            LEFT JOIN gf_tercero t ON cb.banco=t.id_unico 
            WHERE cb.parametrizacionanno = $anno 
            ORDER BY t.razonsocial ASC";
  $tipoAct = $mysqli->query($tipoA);

//consulta para traer los datos a listar
  $tipoA2 = "SELECT cb.id_unico, cb.numerocuenta, cb.banco, t.id_unico, t.razonsocial, cbt.cuentabancaria, cb.cuenta, 
            cta.codi_cuenta, cta.nombre, cta.id_unico  
            FROM gf_cuenta_bancaria cb
            LEFT JOIN gf_tercero t ON cb.banco=t.id_unico
            LEFT JOIN gf_cuenta_bancaria_tercero cbt ON cbt.cuentabancaria=cb.id_unico 
            LEFT JOIN gf_cuenta cta ON cta.id_unico = cb.cuenta 
            WHERE cbt.tercero = $id AND cb.parametrizacionanno = $anno ";
  $tipoAct2 = $mysqli->query($tipoA2);

//consultas para modificar el campo CUENTA BANCARIA
  $tipoA3 = "SELECT cb.id_unico, cb.numerocuenta, cb.banco, t.id_unico, t.razonsocial 
            FROM gf_cuenta_bancaria cb
            LEFT JOIN gf_tercero t ON cb.banco=t.id_unico 
            WHERE cb.parametrizacionanno = $anno 
            ORDER BY t.razonsocial ASC";
  $tipoAct3 = $mysqli->query($tipoA3);

  // llamado al encanbezado del listar


 ?>
<link href="css/select/select2.min.css" rel="stylesheet">

<title>Registrar Cuenta Bancaria Tercero</title>
</head>
<body>
	<div class="container-fluid text-center">	
		<div class="row content">

<!--Lllamado al menu    --> 				
			<?php require_once 'menu.php'; ?>
			<div class="col-sm-8 text-left">
				 <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Cuenta Bancaria Tercero</h2>
<!-- Bot�n volver -->          
           <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
<!-- Nombre del tercero -->
				  <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero))); 
          ?></h4>
<!-- Caja para REGISTRAR la informacion -->
			 <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">				 	
				 	<form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarCuentaBancariaTerJson.php">
  				 		<p align="center" style="margin-bottom: 25px; margin-top:10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                                             
                                                <input type="hidden" name="tercero" id="tercero" value="<?php echo $id ?>">

<!-- Combo CARGO -->
  				 		<div class="form-group"  style="margin-top: -20px;">
    				 			<label for="cuenta" class="col-sm-4 control-label"><strong style="color:#03C1FB;">*</strong>Cuenta Bancaria:</label>
    				 			<select style="display:inline-block" name="cuenta" id="cuenta" class="select2_single form-control" title="Seleccione la cuenta bancaria" onblur="return existente()" required>
    				 				<option value="">Cuenta Bancaria</option>
    				 				<?php while ($mcb = mysqli_fetch_row($tipoAct)) { ?>
    				 					<option value="<?php echo $mcb[0]; ?>">
    				 						<?php echo ucwords((mb_strtolower($mcb[1]." ".$mcb[4]))); ?>
    				 					</option>
    				 				<?php  } ?>
    				 			</select>
                    <input type="hidden" name="tercero" value="<?php echo $id ?>">
                 <button type="submit" class="btn btn-primary sombra" style="margin-left:10px; margin-top:10px">Guardar</button>
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
                  <td style="display: none;">Identificador</td>
                  <td width="30px" align="center"></td>
                  <td><strong>Cuenta Bancaria</strong></td>
                  <?php if($_SESSION['tipo_perfil']=='Compañía') { ?>
                  <td><strong>Cuenta Contable</strong></td>
                  <?php } ?>
                </tr>

                <tr>
                  <th style="display: none;">Identificador</th>
                  <th width="7%"></th>
                  <th>Cuenta Bancaria</th>
                  <?php if($_SESSION['tipo_perfil']=='Compañía') { ?>
                  <th>Cuenta Contable</th>
                  <?php } ?>
                </tr>
              </thead>

              <tbody>   
                <?php 
                while ($row = mysqli_fetch_row($tipoAct2)) { ?>
                  
                  <tr>               
                    <td style="display: none;"><?php echo $row[0]?></td>
                    <td align="center">
                      <?php if(empty($row[9])) { ?>  
                      <a href="#" onclick="javascript:eliminarItem(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                      
                      <a onclick="modificarModal(<?php echo $id;?>,<?php echo $row[5];?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                      <?php }  else { ?>
                      <a href="#" onclick="javascript:eliminarItem(<?php echo $row[0];?>,<?php echo $row[9];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                      
                      <a onclick="modificarModal(<?php echo $id;?>,<?php echo $row[5];?>,<?php echo $row[9];?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                      <?php } ?>
                    </td>
                    <td><?php echo $row[1]." - ".(ucwords(mb_strtolower($row[4])));?></td>
                    <?php if($_SESSION['tipo_perfil']=='Compañía') { ?>
                    <td><?php echo $row[7]." - ".ucwords(mb_strtolower(($row[8])));?></td>
                    <?php } ?>
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
				    <a href="registrar_GF_CUENTA_BANCARIA.php" class="btn btn-primary sombra" style="margin-left:10px; margin-top:5px">CUENTA BANCARIA</a>

          </div>
      </div>
  </div>
</div>

<!--  LLamado al pie de pagina -->	
	<?php require_once 'footer.php'; ?>
<script src="js/select/select2.full.js"></script>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
  </script>
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
            <label style="display:inline-block; width:140px">Cuenta Bancaria:</label>
            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="tipoActmodal" id="tipoActmodal" value class="select2_single form-control" title="Seleccione la cuenta bancaria" required>
                <?php while ($mcb = mysqli_fetch_row($tipoAct3)) { ?>
                      <option value="<?php echo $mcb[0]; ?>">
                        <?php echo ucwords((mb_strtolower($mcb[1]." ".$mcb[4]))); ?>
                      </option>
                <?php  

                 } ?>
            </select> 
          </div>
            <?php 
                if($_SESSION['tipo_perfil']=='Compañía') { 
                    $anno = $_SESSION['anno'];
                 $cc = "SELECT
                        id_unico,
                        codi_cuenta, nombre
                      FROM
                        gf_cuenta
                      WHERE parametrizacionanno = $anno 
                          AND movimiento = '1' AND (clasecuenta = '11' OR clasecuenta = '12' )
                      ORDER BY codi_cuenta ASC";
                $cc = $mysqli->query($cc);?>
            <div style="margin-top: 13px;">
            <label style="display:inline-block; width:140px">Cuenta Contable:</label>
            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="cuenta_cont" id="cuenta_cont" class="select2_single form-control" title="Seleccione la cuenta contable">
                <?php while ($mcb = mysqli_fetch_row($cc)) { ?>
                      <option value="<?php echo $mcb[0]; ?>">
                        <?php echo ucwords((mb_strtolower($mcb[1]." - ".$mcb[2]))); ?>
                      </option>
                <?php  

                 } ?>
            </select>
            </div>
            <?php } else { ?>
            <input type="hidden" id="cuenta_cont" name="cuenta_cont" value="0">  
            <?php } ?>
              <input type="hidden" id="id" name="id">   
              <input type="hidden" id="cuentaA" name="cuentaA">   
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

<!--  MODAL para los mensajes de la opcion  eliminar -->

   <div class="modal fade" id="myModal" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">      
        <p>¿Desea eliminar el registro seleccionado de Cuenta Bancaria Tercero?</p>
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
<div class="modal fade" id="myModalCuentaE" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        <p>Cuenta Bancaria Ya Existe.</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="verCuentaE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>


<!-- librerias -->
 	
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>

<!-- Funci�n para retornar al formulario principal. -->
<script type="text/javascript">

  $("#ver5").click(function(){
   
      document.location = "GF_CUENTA_BANCARIA_TERCERO.php?id=<?php echo $id ?>";

   
 });

$("#ver1").click(function(){
     document.location = "GF_CUENTA_BANCARIA_TERCERO.php?id=<?php echo $id ?>";
   
   
 });

$("#ver2").click(function(){
      document.location = "GF_CUENTA_BANCARIA_TERCERO.php?id=<?php echo $id ?>";
    
   
 });

</script>

<!-- Funci�n para la opcion modificar. -->

   <script type="text/javascript">
  function modificarModal(id,tipoA, cta){
    
    
    $("#cuenta_cont").val(cta);
    $("#tipoActmodal").val(tipoA);
    document.getElementById('id').value = id;
    $("#cuentaA").val(tipoA);
      $("#myModalUpdate").modal('show');
  }
  function modificarItem()
    {
      var result = '';
      var id= document.getElementById('id').value; 
      var tipoActi= document.getElementById('tipoActmodal').value;
      var cuentaA= document.getElementById('cuentaA').value;
      var cuenta = document.getElementById('cuenta_cont').value;
      console.log(cuentaA);
      $.ajax({
        type:"GET",
        url:"json/modificarCuentaBanTerJson.php?p1="+id+"&p2="+tipoActi+"&p3="+cuenta+"&p4="+cuentaA,
        
        
        success: function (data) {
          result = JSON.parse(data);
          console.log(result);
          if(result==1){
            $("#myModal5").modal('show');
            $("#ver5").click(function(){
              $("#myModal5").modal('hide');
              
            });
          }else{
              if(result==2){
            $("#myModal6").modal('show');
            $("#ver6").click(function(){
              $("#myModal6").modal('hide');
              
            });
            }else{
              if(result==3){
            $("#myModalCuentaE").modal('show');
            $("#verCuentaE").click(function(){
              $("#myModalCuentaE").modal('hide');
              
            });
          } else {
              $("#myModal6").modal('show');
            $("#ver6").click(function(){
              $("#myModal6").modal('hide');
              
            });
             }
            }
        }
    }
      });
    }

</script>

<!-- Funci�n para la opcion eliminar -->

<script type="text/javascript">
  function eliminarItem(id, cuenta)
  {
   var result = '';
   $("#myModal").modal('show');
   $("#ver").click(function(){
    $("#myModal").modal('hide');
    $.ajax({
      type:"GET",
      url:"json/eliminarCuentaBanTer.php?id="+id+"&cta="+cuenta,
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