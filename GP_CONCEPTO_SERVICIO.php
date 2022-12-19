<?php
require_once ('Conexion/conexion.php');

$id= $_GET['id'];
//Tipo Servicio
$servicio= "SELECT id_unico, nombre FROM gp_tipo_servicio WHERE md5(id_unico)='$id' ";
$servicio= $mysqli->query($servicio);
$rowSer = mysqli_fetch_row($servicio);
///concepto
$concepto="SELECT c.id_unico, c.nombre, tc.nombre "
        . "FROM gp_concepto c "
        . "LEFT JOIN gp_tipo_concepto tc ON c.tipo_concepto = tc.id_unico "
        . "ORDER BY c.nombre ASC";
$concepto = $mysqli->query($concepto);

///LISTAR

$resul = "SELECT cs.id_unico, c.id_unico, c.nombre, tc.nombre, cs.tipo_servicio "
        . "FROM gp_concepto_servicio cs "
        . "LEFT JOIN gp_concepto c ON cs.concepto= c.id_unico "
        . "LEFT JOIN gp_tipo_concepto tc ON c.tipo_concepto = tc.id_unico "
        . "WHERE md5(cs.tipo_servicio)='$id' ";
$resultado = $mysqli->query($resul);

require_once 'head_listar.php'; ?>
<title>Concepto Servicio</title>
</head>
<body> 
    <div class="container-fluid text-center">
	<div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top:-3px">Concepto Servicio</h2>
                <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Tipo Servicio:<?php echo ucwords((strtolower($rowSer[1]))); ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GP_CONCEPTO_SERVICIOJson.php">
                        <input type="hidden" id="servicio" value="<?php echo $rowSer[0]?>" name="servicio">
                        <input type="hidden" id="id" value="<?php echo $rowSer[0]?>" name="id">
                        <p align="center" style="margin-bottom: 25px; margin-top:0px; margin-left: 40px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div  align = "center" class="form-group form-inline">
                            <div class="form-group form-inline ">
                                <label for="concepto" class="control-label col-sm-4" style="width:200px; display: inline;"><strong style="color:#03C1FB;">*</strong>Concepto:</label>
                                <select name="concepto" id="concepto"  class="form-control" title="Seleccione concepto" required  style="display: inline; width: 250px">
                                    <option value="">Concepto</option>
                                    <?php while($rowT = mysqli_fetch_row($concepto)){?>
                                    <option value="<?php echo $rowT[0] ?>"><?php echo ucwords(strtolower($rowT[1].' - '.$rowT[2]));}?></option>;
                                </select> 
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: 0px; margin-bottom: 10px; ">Guardar</button>

                            </div>
                        </div>
                    </form>
                </div>
               <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Concepto</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Concepto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while($row = mysqli_fetch_row($resultado)){?>
                                
                                <tr>
                                    <td style="display: none;"><?php echo $row[0]?></td>    
                                    <td><a  href="#" onclick="javascript:eliminar(<?php echo $row[0]?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a onclick="modificarModal(<?php echo $row[0].','.$row[1].','.$row[4]?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td><?php echo  (ucwords(strtolower($row[2].' - '.$row[3]))); ?></td> 
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
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
                <p>¿Desea eliminar el registro seleccionado de concepto servicio?</p>
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
<!--  MODAL y opcion  MODIFICAR  informacion  -->  
<div class="modal fade" id="myModalUpdate" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content client-form1">
      <div id="forma-modal" class="modal-header">       
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
      </div>
      <div class="modal-body ">
        <form  name="form" method="POST" action="javascript:modificarItem()">
            <input type="hidden" name="idm" id="idm">
            <input type="hidden" name="serviciom" id="serviciom">
          <div class="form-group" style="margin-top: 13px; margin-left: -10px">
                <label for="tarifa"  style="margin-left: 10px;display:inline-block; width:140px;" ><strong style="color:#03C1FB;">*</strong>Concepto:</label></td>
                <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="conceptom" id="conceptom" class="form-control" title="Seleccione concepto" required>
                <?php 
                    ///concepto
                    $conceptom="SELECT c.id_unico, c.nombre, tc.nombre "
                            . "FROM gp_concepto c "
                            . "LEFT JOIN gp_tipo_concepto tc ON c.tipo_concepto = tc.id_unico "
                            . "ORDER BY c.nombre ASC";
                    $conceptom = $mysqli->query($conceptom);
                    while ($rowTa = mysqli_fetch_row($conceptom)) { ?>
                      <option value="<?php echo $rowTa[0]; ?>">
                        <?php echo ucwords((strtolower($rowTa[1].' - '.$rowTa[2]))); ?>
                      </option>
                <?php } ?>
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
<div class="modal fade" id="myModal7" role="dialog" align="center" >
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
 <?php require_once 'footer.php'; ?>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>

<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminar_GP_CONCEPTO_SERVICIOJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true){
                    $("#myModal1").modal('show');
                    $("#ver1").click(function(){
                      $("#myModal1").modal('hide');
                      document.location = 'GP_CONCEPTO_SERVICIO.php?id=<?php echo $id;?>';
                    });
                  } else{ 
                      $("#myModal2").modal('show');
                      $("#ver2").click(function(){
                      $("#myModal2").modal('hide');
                      document.location = 'GP_CONCEPTO_SERVICIO.php?id=<?php echo $id;?>';
                    });
                  }
                  }
              });
          });
      }
      function modificarModal(id,concepto, servicio){
    
            $("#idm").val(id);
            $("#conceptom").val(concepto);
            $("#serviciom").val(servicio);
            
              $("#myModalUpdate").modal('show');
          }
      
      function modificarItem()
    {
      var result = '';
       var id= document.getElementById('idm').value;
      var servicio= document.getElementById('serviciom').value;
      var concepto= document.getElementById('conceptom').value;
      
      $.ajax({
        type:"GET",
        url:"json/modificar_GP_CONCEPTO_SERVICIOJson.php?id="+id+"&servicio="+servicio+"&concepto="+concepto,
        success: function (data) {
          result = JSON.parse(data);
          if(result=='1'){
                $("#myModal5").modal('show');
                $("#ver5").click(function(){
                    document.location = 'GP_CONCEPTO_SERVICIO.php?id=<?php echo $id;?>';
                });
              }else{
                if(result=='3'){
                  $("#myModal7").modal('show');
                $("#ver7").click(function(){
                  document.location = 'GP_CONCEPTO_SERVICIO.php?id=<?php echo $id;?>';
                });
                }else {
                $("#myModal6").modal('show');
                 $("#ver6").click(function(){
                  document.location = 'GP_CONCEPTO_SERVICIO.php?id=<?php echo $id;?>';
                });
              }
              }
        }
      });
    }
 </script> 
        
 

</body>
</html>


