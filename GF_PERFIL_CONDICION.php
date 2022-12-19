<?php
require_once ('Conexion/conexion.php');
session_start();
require_once 'head_listar.php'; 

$perfil = $_SESSION['tipo_perfil'];
$idp= "SELECT id_unico, nombre FROM gf_perfil WHERE nombre = '$perfil'";
$perfil1 = $mysqli->query($idp);
$rowP = mysqli_fetch_row($perfil1);
$idperfil= ($rowP[0]);

?>
<title>Perfil Condición</title>
</head>
<body> 
  <div class="container-fluid text-center"> 
    <div class="row content">
      <!--Lllamado al menu    -->         
      <?php require_once 'menu.php'; ?>
      <div class="col-sm-8 text-left" style="margin-top:-5px">
         <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px; height:45px;">Perfil condición</h2>
          <!-- Botón volver -->          
           <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
          <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px;"><?php echo 'Perfil: '.$perfil;?></h5>
          <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top:5px;" class="client-form">         
            <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarPerfilCondicionJson.php">
              <p align="center" style="margin-bottom: 25px; margin-top:5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                <input type="hidden" name="perfil" value="<?php echo $idperfil; ?>">
          <div class="form-group" class="col-sm-4" style="margin-top:-10px;" align="center">
                  <label for="condicion" class="control-label"><strong style="color:#03C1FB;">*</strong>Condición:</label>
                  <select style="display:inline-block; width:250px; margin-right:40px" name="condicion" id="condicion" class="form-control" title="Seleccione el condición" required="required">
                    <option value="">Condición</option>
                    <?php 
                      $cond = "SELECT id_unico, nombre FROM gf_condicion ORDER BY nombre ASC";
                      $condicion = $mysqli->query($cond);

                      while ($rowCondicion = mysqli_fetch_assoc($condicion)) { ?>
                        <option value="<?php echo $rowCondicion["id_unico"]; ?>">
                          <?php echo ucwords(strtolower($rowCondicion["nombre"])); ?>
                        </option>
                    <?php  } ?>
                  </select>
                  <label for="condicion" class="control-label"><strong style="color:#03C1FB;">*</strong>Obligatorio:</label>
                  <div style="display:inline; margin-right:20px"> 
                      <input type="radio" name="obligatorio" id="obligatorio" value="0">Sí
                      <input type="radio" name="obligatorio" id="obligatorio" value="1" checked="true">No

                  </div>
                 <button type="submit" class="btn btn-primary sombra" style="margin-left:10px; margin-top: 10px;">Guardar</button>
              </div>          
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
                <td class="cabeza"><strong>Condición</strong></td>
                <td class="cabeza"><strong>Obligatorio</strong></td>
                </tr>

                <tr>
                <th class="oculto">Identificador</th>
                <th width="7%"></th>
                <th>Condición</th>
                <th>Obligatorio</th>
                </tr>
              </thead>

              <tbody>   
                <?php
                //consulta para traer los datos a listar
                  $perC = "SELECT pc.perfil, pc.condicion, c.nombre, pc.obligatorio, pc.id_unico 
                      FROM gf_perfil_condicion pc 
                      LEFT JOIN gf_condicion c ON c.id_unico = pc.condicion
                      WHERE pc.perfil = $idperfil";
                  $perfilC = $mysqli->query($perC);

                while ($rowPerCond = mysqli_fetch_row($perfilC)) { ?>
                  
                  <tr>               
                    <td style="display: none;"><?php echo $rowPerCond[4]?></td>
                    <td align="center" class="campos">
                      <a href="#" onclick="javascript:eliminarItem(<?php echo $rowPerCond[4]?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                      <a onclick="modificarModal(<?php echo $rowPerCond[4].','.$rowPerCond[0].','.$rowPerCond[1].','.$rowPerCond[3]?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                    </td>
                    <td class="campos"><?php echo ucwords(strtolower($rowPerCond[2]));?></td>
                    <td class="campos"><?php if($rowPerCond[3]=='0'){ echo 'Sí';} else { echo 'No';}?></td>
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
          <h2 class="titulo" align="center" style=" font-size:17px; height:45px;">Adicional</h2>
          <div  align="center">
            <a href="Registrar_GF_CONDICION.php" class="btn btn-primary btnInfo">CONDICIÓN</a>          
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
          <input type="hidden" id="perfil" name="perfil">   
          <input type="hidden" id="id" name="id">   
          <div class="form-group" style="margin-top: 13px;">
            <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Condición:</label>
            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="condicionM" id="condicionM" class="form-control" title="Seleccione condición" required>
                <?php 
                      $cond1 = "SELECT id_unico, nombre FROM gf_condicion ORDER BY nombre ASC";
                      $condicion1 = $mysqli->query($cond1);

                      while ($rowCondicion1 = mysqli_fetch_assoc($condicion1)) { ?>
                        <option value="<?php echo $rowCondicion1["id_unico"]; ?>">
                          <?php echo ucwords(strtolower($rowCondicion1["nombre"])); ?>
                        </option>
                    <?php  } ?>
            </select>          
               
              </div>
          <div class="form-group" style="margin-top: 13px;">
              
                 <label for="obligatorio"  style="margin-left: 10px;display:inline-block; width:140px;" ><strong style="color:#03C1FB;">*</strong>Obligatorio:</label></td>
             
                  <div align="left" style="display:inline-block; width:250px; margin-bottom:15px; height:40px">
                  <input  type="radio" name="obli" id="obli1"  value="0" checked>SI
                  <input  type="radio" name="obli" id="obli2" value="1" checked>NO
                 </div>
          </div>  
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
  <div class="modal fade" id="myModal8" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
        <p>La Condici&oacuten Tercero ingresado ya existe.</p>
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
        <p> &iquest;Desea eliminar el registro seleccionado de Perfil Condici&oacute;n?</p>
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



<!-- Función para retornar al formulario principal. -->
<script type="text/javascript">

  $("#ver5").click(function(){
   
   document.location = "GF_PERFIL_CONDICION.php";
 });

$("#ver1").click(function(){
   
   document.location = "GF_PERFIL_CONDICION.php";
 });

$("#ver2").click(function(){
   
   document.location = "GF_PERFIL_CONDICION.php";
 });

</script>

<!-- Función para la opcion modificar. -->

   <script type="text/javascript">
  function modificarModal(id,perfil,condicion,obligatorio){
    
    $("#condicionM").val(condicion);
    document.getElementById('condicionM').value= condicion;
    if(obligatorio===0){
                document.getElementsByName("obli")[0].checked = true;
            }else {
                document.getElementsByName("obli")[1].checked = true;
            }
    document.getElementById('id').value = id;
    document.getElementById('perfil').value = perfil;
      $("#myModalUpdate").modal('show');
  }
  function modificarItem()
    {
      var result = '';
      var id= document.getElementById('id').value; 
      var perfil= document.getElementById('perfil').value;
      var condicion= document.getElementById('condicionM').value;
      if (document.getElementById('obli1').checked)
            {
             var obligatorio='0';
            }
        else {
            var obligatorio='1';
        }
      $.ajax({
        type:"GET",
        url:"json/modificarPerfilCondJson.php?p1="+id+"&p2="+perfil+"&p3="+condicion+"&p4="+obligatorio,
        success: function (data) {
          result = JSON.parse(data);
          
          if(result=='3'){
            $("#myModal8").modal('show');
            $("#ver8").click(function(){
              $("#myModal8").modal('hide');
              $("#myModalUpdate").modal('hide');
            });
          }else{
            if(result==true){
              $("#myModal5").modal('show');
            $("#ver5").click(function(){
              $("#myModal5").modal('hide');
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
      url:"json/eliminarPerfilCond.php?id="+id,
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
</body>
</html>


