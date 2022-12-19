<?php
    require_once ('Conexion/conexion.php');
    $id1= $_GET['id'];
    #CARACTERISTICA
    $caracteristica = "SELECT  c.id_unico, c.nombre, td.nombre "
                    . "FROM gg_caracteristica c "
                    . "LEFT JOIN gf_tipo_dato td ON c.tipo_dato = td.id_unico "
                    . "ORDER BY c.nombre ASC";
    $caracteristica = $mysqli->query($caracteristica);
    
    #LISTAR
    $queryCC = "SELECT dc.id_unico, dc.tipo_proceso, dc.caracteristica, c.nombre, td.nombre "
            . "FROM gg_confi_caracteristica dc "
            . "LEFT JOIN gg_caracteristica c ON dc.caracteristica = c.id_unico "
            . "LEFT JOIN gf_tipo_dato td ON c.tipo_dato = td.id_unico "
            . "WHERE md5(dc.tipo_proceso)= '$id1'"; 
    $resultado = $mysqli->query($queryCC);
    
 ?>
 <?php 
$id2 = $_GET['id'];
$queryT = "SELECT id_unico, nombre, identificador FROM gg_tipo_proceso WHERE md5(id_unico) = '$id2'";
$tipoD = $mysqli->query($queryT);
$rowD = mysqli_fetch_assoc($tipoD);


 ?>
<?php require_once 'head_listar.php'; ?>
<title>Proceso Característica</title>
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
label#caracteristica-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}
body{
    font-size: 12px;
}
</style>
</head>
<body>
    
    <div class="container-fluid text-center">
	<div class="row content">
            <?php require_once 'menu.php'; ?>
                 


            <div class="col-sm-10 text-left" style="margin-top:-10px">	
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:10px">Proceso Característica</h2>
              <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; margin-top:-5px;vertical-align:middle;text-decoration:none" title="Volver"></a>

                  <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-20px;  background-color: #0e315a; color: white; border-radius: 5px">Proceso:<?php echo ucwords((strtolower($rowD['identificador'].' - '.$rowD['nombre']))); 
          ?></h5>

                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">         
                    <form name="form" id="form"  class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonProcesos/registrar_GG_PROCESO_CARACTJson.php">
                        <p align="center" style="margin-bottom: 25px; margin-top:10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div align="center" class="form-group form-inline" style="margin-top:-20px; margin-left:0px">
                            <input type="hidden" name="proceso" id="proceso" value="<?php echo $rowD['id_unico']?>">
                            <div class="form-group form-inline " style="margin-left:0px" >
                                <label for="caracteristica" class="control-label"><strong style="color:#03C1FB;">*</strong>Característica:</label>
                                <input type="hidden" name="caracteristica" id="caracteristica" title="Seleccione característica" required="required">
                                <select class="select2_single form-control" style="width:280px" name="caracteristica1" id="caracteristica1"  title="Seleccione caracteristica" required onchange="llenar();">
                                    <option value="">Característica</option>
                                    <?php while($row = mysqli_fetch_row($caracteristica)){?>
                                    <option value="<?php echo $row[0] ?>"><?php echo ucwords((strtolower($row[1].' - '.$row[2])));}?></option>;
                                </select> 
                             </div>
                            <div class="form-group form-inline " style="margin-left: 10px;">
                                <button type="submit" class="btn btn-primary sombra" style="margin-left:10px; margin-top:10px">Guardar</button>
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
                                <td><strong>Característica</strong></td>
                            </tr>
                            <tr>
                                <th style="display: none;">Identificador</th>
                                <th width="7%"></th>
                                <th>Característica</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while($row = mysqli_fetch_row($resultado)){?>
                            <tr>
                                <td style="display: none;"><?php echo $row[0]?></td>    
                            <td><a  href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                    <a onclick="modificarModal(<?php echo $row[0].','.$row[1].','.$row[2];?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a></td>
                                <td><?php echo (ucwords(strtolower($row[3].' - '.$row[4]))); ?></td>
                           </tr>
                            <?php } ?>
                        </tbody>
                    </table>
            </div>
        </div>
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
  function llenar(){
      var caracteristica = document.getElementById('caracteristica1').value;
      document.getElementById('caracteristica').value= caracteristica;
  }
  
  </script>
<!--  MODAL y opcion  MODIFICAR  informacion  -->  
<div class="modal fade" id="myModalUpdate" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content client-form1">
      <div id="forma-modal" class="modal-header">       
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
      </div>

      <?php 
        #CARACTERISTICA
        $caracteristica1 = "SELECT  c.id_unico, c.nombre, td.nombre "
                    . "FROM gg_caracteristica c "
                    . "LEFT JOIN gf_tipo_dato td ON c.tipo_dato = td.id_unico "
                    . "ORDER BY c.nombre ASC";
        $caracteristica1 = $mysqli->query($caracteristica1);
       ?>

      <div class="modal-body ">
        <form  name="form" method="POST" action="javascript:modificarItem()">
           <input type="hidden" name=id id="id">
           <input type="hidden" name="procesom" id="procesom">
           <div class="form-group" style="margin-top: 13px;">
            <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Característica:</label>
            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="caracteristicam" id="caracteristicam" class="select2_single form-control" title="Seleccione característica" required>
                <?php while ($modTer = mysqli_fetch_row($caracteristica1)) { ?>
                      <option value="<?php echo $modTer[0]; ?>">
                        <?php echo ucwords((strtolower($modTer[1]).' - '.$modTer[2])); ?>
                      </option>
                <?php  

                 } ?>
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

 
   
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de proceso característica?</p>
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
           <p>El registro ingresado ya existe..</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver7" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

     <?php require_once 'footer.php'; ?>
<script type="text/javascript" src="js/menu.js"></script>
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
                  url:"jsonProcesos/eliminar_GG_PROCESO_CARACTJson.php?id="+id,
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
      function modificarModal(id,proceso,caracteristica){
            $("#caracteristicam").val(caracteristica);
            $("#id").val(id);
            $("#procesom").val(proceso);
              $("#myModalUpdate").modal('show');
          }
          function modificarItem()
    {
      var result = '';
      var id= document.getElementById('id').value;
      var proceso= document.getElementById('procesom').value;
      var caracteristica= document.getElementById('caracteristicam').value;
      
      $.ajax({
        type:"GET",
        url:"jsonProcesos/modificar_GG_PROCESO_CARACTJson.php?id="+id+"&proceso="+proceso+"&caracteristica="+caracteristica,
        success: function (data) {
          result = JSON.parse(data);
        if(result=='3'){
            $("#myModalUpdate").modal('hide');
                $("#myModal7").modal('show');
                $("#ver7").click(function(){

                  $("#myModal7").modal('hide');
                   document.location = 'GG_PROCESO_CARACTERISTICA.php?id=<?php echo $id1;?>';

                });
        }  else { 
            if(result==true){
                $("#myModalUpdate").modal('hide');
                $("#myModal5").modal('show');
                $("#ver5").click(function(){

                  $("#myModal5").modal('hide');
                   document.location = 'GG_PROCESO_CARACTERISTICA.php?id=<?php echo $id1;?>';

                });
              }else{
                 $("#myModalUpdate").modal('hide'); 
                $("#myModal6").modal('show');
                $("#ver6").click(function(){

                  $("#myModal6").modal('hide');
                   document.location = 'GG_PROCESO_CARACTERISTICA.php?id=<?php echo $id1;?>';

                });

              }
          }
        }
      });
    }
  </script>
  <script type="text/javascript">
      function modal()
      {
         $("#myModal").modal('show');
      }
  </script>
  
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'GG_PROCESO_CARACTERISTICA.php?id=<?php echo $id1;?>';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'GG_PROCESO_CARACTERISTICA.php?id=<?php echo $id1;?>';
      });
    
  </script>

</body>
</html>



