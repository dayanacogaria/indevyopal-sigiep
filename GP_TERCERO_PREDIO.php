<?php
require_once ('Conexion/conexion.php');

$id= $_GET['id'];
//PREDIO
$pred= "SELECT id_unico, codigo_catastral FROM gp_predio1 WHERE md5(id_unico)='$id' ";
$predio= $mysqli->query($pred);
$rowP = mysqli_fetch_row($predio);
//TERCERO
$ter="SELECT  IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
        (ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', "
        . "id_unico, numeroidentificacion FROM gf_tercero ter ORDER BY NOMBRE ASC";
$tercero = $mysqli->query($ter);
$row = mysqli_fetch_row($tercero);

//LISTAR

$resul = "SELECT IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) "
        . "IS NULL OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' , "
        . "(ter.razonsocial),CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)), "
        . "ter.id_unico, ter.numeroidentificacion, tp.tercero, tp.predio, tp.propietario, tp.porcentaje "
        . "FROM gp_tercero_predio tp "
        . "LEFT JOIN gf_tercero ter ON tp.tercero = ter.id_unico "
        . "LEFT JOIN gp_predio1 p ON tp.predio =p.id_unico "
        . "WHERE md5(tp.predio)='$id' ";
$resultado = $mysqli->query($resul);

require_once 'head_listar.php'; ?>
<title>Tercero Predio</title>
</head>
<body> 
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
label#Tercero-error, #Propietario-error{
    display: inline-block;
    color: #155180;
    font-weight: normal;
    font-style: italic;
    width: 200px;

}
</style>
<?php
#PORCENTAJE
$porcentaje= "SELECT SUM(porcentaje) FROM gp_tercero_predio WHERE predio = '$rowP[0]'";
$porcentaje = $mysqli->query($porcentaje);
$porcentaje = mysqli_fetch_row($porcentaje);
$porcentaje = $porcentaje[0];
$valorP = (100-$porcentaje);
$valorPM =sprintf("%01.2f", $valorP);
if($valorPM==0) {
?>
<script>
    $(function() {
        document.getElementById('porcentaje').value = '';
       document.getElementById('porcentaje').disabled=true;
    })
</script>
<?php } ?>

    <div class="container-fluid text-center">
	<div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top:-3px">Tercero Predio</h2>
                <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Predio:<?php echo ucwords((strtoupper($rowP[1]))); ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GP_TERCERO_PREDIOJson.php">
                        <input type="hidden" id="predio" value="<?php echo $rowP[0]?>" name="predio">
                        <p align="center" style="margin-bottom: 25px; margin-top:0px; margin-left: 40px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline" style="margin-top:-20px">
                            <div class="form-group form-inline ">
                                <label for="Tercero" class="control-label col-sm-4" style="width:200px; display: inline; margin-top: -15px"><strong style="color:#03C1FB;">*</strong>Tercero :</label>
                            </div>
                            <div class="form-group form-inline " style="margin-left:10px;">
                                <select name="Tercero" id="Tercero"  class="select2_single form-control" style = "width:250px" title="Seleccione tercero" required  style="display: inline;">
                                    <option value>Tercero</option>
                                    <?php while($rowT = mysqli_fetch_assoc($tercero)){?>
                                    <option value="<?php echo $rowT['id_unico'] ?>"><?php echo ucwords((strtolower($rowT['NOMBRE'].' ('.$rowT["numeroidentificacion"].')')));}?></option>;
                                </select> 
                           </div>
                        <div class="form-group form-inline " style="margin-top: -5px;">
                            <label for="Propietario" class="control-label col-sm-2" style="width:200px; margin-top: -8px"><strong style="color:#03C1FB;">*</strong>Propietario:</label>
                            <input  type="radio" name="Propietario" id="Propietario"  value="1" onclick="cambioM()" >SI
                            <input  type="radio" name="Propietario" id="Propietario" value="2" checked onclick="cambioN()">NO
                        </div>
                            <div class="form-group form-inline " style="margin-top: 10px; display: none " id="porcentajediv" >
                            <label for="porcentaje" class="control-label col-sm-2" style="width:150px;"><strong style="color:#03C1FB;">*</strong>Porcentaje Propiedad:</label>
                            <input type="text" name="porcentaje" id="porcentaje" class="form-control " maxlength="5"  style="width:100px" onkeypress="return validarNum1(event, true)">
                        </div>
                        <div class="form-group form-inline " style="margin-top: 10px;">
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: 0px; margin-bottom: 10px; ">Guardar</button>
                            <script>
                                function cambioM(){
                                   document.getElementById('porcentajediv').style.display = 'inline-block'; 
                                   $('#porcentaje').attr("required", "required");
                                   document.getElementById('porcentaje').value = <?php echo $valorPM;?>
                                }
                                function cambioN(){
                                   document.getElementById('porcentajediv').style.display = 'none'; 
                                   $('#porcentaje').removeAttr('required');
                                   document.getElementById('porcentaje').value='';
                                }
                            </script>
                            <input type="hidden" name="MM_insert" >
                        </div>
                            <div class="form-group form-inline " style="margin-left:10px;" id="porc">
                                
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
                                    <td><strong>Nombre Tercero</strong></td>
                                    <td><strong>Propietario</strong></td>
                                    <td><strong>Porcentaje Propiedad</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Nombre Tercero</th>
                                    <th>Propietario</th>
                                    <th>Porcentaje Propiedad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while($row = mysqli_fetch_row($resultado)){?>
                                
                                <tr>
                                    <td style="display: none;"><?php echo $row[1]?></td>    
                                    <td><a  href="#" onclick="javascript:eliminar(<?php echo $row[3].','.$row[4]?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <?php if (empty($row[6])) { ?>
                                        <a onclick="modificarModal(<?php echo $row[3].','.$row[4].','.$row[5]?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                        <?php }  else { ?>
                                        <a onclick="modificarModal(<?php echo $row[3].','.$row[4].','.$row[5].','.$row[6]?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        
                                         <?php echo ucwords(strtolower($row[2].' - '.$row[0])); ?>
                                        
                                    
                                    </td>
                                    <td>
                                        <?php 
                                        switch ($row[5]) {
                                            case 1: 
                                                echo "Si";?>
                                                
                                            <?php
                                                break;
                                           case 2: 
                                                    echo "No";?>
                                                   

                                        <?php
                                            break;
                                        } ?>
                                    </td>  
                                    <td>
                                       <?php if(empty($row[6])){
                                           echo '';
                                       } else {
                                           echo $row[6];
                                       }?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="text-left">
                            <label>Porcentaje asignado: <i><?php $porc =sprintf("%01.2f", $porcentaje); echo $porc;?>%</i></label><br/>
                            <label>Porcentaje por asignar: <i><?php echo $valorPM;?>%</i></label>
                        </div>
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
  var validarNum1 = function (event){
    event = event || window.event;
    var charCode = event.keyCode || event.which;
    var first = (charCode <= 57 && charCode >= 48);
    var numero = document.getElementById('porcentaje').value;
    var char = parseFloat(String.fromCharCode(charCode));
    var num = parseFloat(numero+char);
    var com = parseFloat(<?php echo sprintf("%01.2f", $valorP);?>);
    
        var match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
        var dec = match[0].length;
        if(dec<=3){
            if(num < com){
                if (charCode ==46){
                    var element = event.srcElement || event.target;
                    if(element.value.indexOf('.') == -1){
                    return (charCode =46);
                    }else{
                       return first; 
                    }
                    } else {
                    return first;
                }
            } else {
                if(num <=com){
                    return first;
                }else{
                    return false;
                }
            }
        } else { 
            return false;
        }
    
       
    
}
  </script> 
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de tercero predio?</p>
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

<!-- Consulta para modificar el combo TERCERO   -->
      <?php 
       $ter="SELECT  IF(CONCAT(nombreuno,' ', nombredos, ' ', apellidouno, ' ', apellidodos)='',"
        . "(razonsocial),CONCAT(nombreuno,' ',nombredos,' ',apellidouno,' ',apellidodos)) AS 'NOMBRE', "
        . "id_unico, numeroidentificacion FROM gf_tercero ORDER BY NOMBRE ASC";
      $tercero = $mysqli->query($ter);
      
       ?>

      <div class="modal-body ">
        <form  name="form" method="POST" action="javascript:modificarItem()">
            <input type="hidden" name="pred" id="pred">
            <input type="hidden" name="tercA" id="tercA">
          <div class="form-group" style="margin-top: 13px;">
            <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Tercero:</label>
            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="tipoActmodal" id="tipoActmodal" class="form-control" title="Seleccione tercero" required>
                <?php while ($modTer = mysqli_fetch_row($tercero)) { ?>
                      <option value="<?php echo $modTer[1]; ?>">
                        <?php echo ucwords((strtolower($modTer[0]).'('.$modTer[2].')')); ?>
                      </option>
                <?php  

                 } ?>
            </select>                                
          </div>
          <div class="form-group" style="margin-top: 13px;">
              
                 <label for="valor"  style="margin-left: 10px;display:inline-block; width:140px;" ><strong style="color:#03C1FB;">*</strong>Propietario:</label></td>
             
                  <div align="left" style="display:inline-block; width:250px; margin-bottom:15px; height:40px">
                  <input type="hidden" name="valorA" id="valorA" value="" >
                  <input  type="radio" name="prop" id="prop1"  value="1" checked onclick="cambioMM()">SI
                  <input  type="radio" name="prop" id="prop2" value="2" checked onclick="cambioNM()">NO
                 </div>
                <input type="hidden" name="porcentajeA" id="porcentajeA">
                <script>
                    function cambioMM(){
                       document.getElementById('divPorcentaje').style.display = 'inline-block'; 
                       $('#porcentajeM').attr("required", "required");
                       var porcentajeA = document.getElementById('porcentajeA').value;
                       document.getElementById('porcentajeM').value=porcentajeA;
                    }
                    function cambioNM(){
                       document.getElementById('divPorcentaje').style.display = 'none'; 
                       $('#porcentaje').removeAttr('required');
                       document.getElementById('porcentajeM').value='';
                    }
                </script>
          </div>
            <div class="form-group" style="margin-top: -12px;" name="divPorcentaje" id="divPorcentaje">
              
                 <label for="porcentaje"  style="margin-left: 10px;display:inline-block; width:140px;" ><strong style="color:#03C1FB;">*</strong>Porcentaje:</label></td>
                <input type="text" name="porcentajeM" id="porcentajeM"  style="display:inline-block; width:250px; margin-bottom:15px; height:40px" onkeypress="return validarNum2(event, true)" maxlength="5">
                 
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
<script>
    
    var validarNum2 = function (event){
    event = event || window.event;
    var charCode = event.keyCode || event.which;
    var first = (charCode <= 57 && charCode >= 48);
    var numero = document.getElementById('porcentajeM').value;
    console.log(numero);
    var char = parseFloat(String.fromCharCode(charCode));
    var num = parseFloat(numero+char);
    var valor1 = document.getElementById('porcentajeA').value;
    if(valor1==''){
        valor1=0;
    }
    var valor1 = parseFloat(valor1);
    var valor2 = <?php echo sprintf("%01.2f", $valorP);?>;
    var valor2 = parseFloat(valor2);
    var com = parseFloat(valor1+valor2);
        var match = ('' + num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
        var dec = match[0].length;
        if(dec<=3){
            if(num < com){
                if (charCode ==46){
                    var element = event.srcElement || event.target;
                    if(element.value.indexOf('.') == -1){
                    return (charCode =46);
                    }else{
                       return first; 
                    }
                    } else {
                    return first;
                }
            } else {
                if(num <=com){
                    return first;
                }else{
                    return false;
                }
            }
        } else { 
            return false;
        }
    
       
    
}
</script>

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
     <?php require_once 'footer.php'; ?>
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

<script type="text/javascript">
      function eliminar(id1,id2)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminar_GP_TERCERO_PREDIOJson.php?id1="+id1+"&id2="+id2,
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
      function modificarModal(tercero,predio,propietario, porcentaje){
    
            $("#tipoActmodal").val(tercero);
            $("#tercA").val(tercero);
            document.getElementById('pred').value = predio;
            valor = propietario;
            $('#valorA').val(valor);
            $('#porcentajeM').val(porcentaje);
            $('#porcentajeA').val(porcentaje);
            if(propietario===1){
                document.getElementsByName("prop")[0].checked = true;
                document.getElementById("divPorcentaje").style.display = 'inline-block'; 
            }else {
                document.getElementsByName("prop")[1].checked = true;
                document.getElementById("divPorcentaje").style.display = 'none'; 
            }
              $("#myModalUpdate").modal('show');
          }
      
      function modificarItem()
    {
      var result = '';
       var tercA= document.getElementById('tercA').value;
      var predio= document.getElementById('pred').value;
      var tercero= document.getElementById('tipoActmodal').value;
      var porcentaje= document.getElementById('porcentajeM').value;
      if (document.getElementById('prop1').checked)
            {
             var propietario='1';
            }
        else {
            var propietario='2';
        }
      $.ajax({
        type:"GET",
        url:"json/modificar_GP_TERCERO_PREDIOJson.php?predio="+predio+"&tercero="+tercero+"&propietario="+propietario+"&tercA="+tercA+"&porcentaje="+porcentaje,
        success: function (data) {
          result = JSON.parse(data);
          if(result==true){
            $("#myModalUpdate").modal('hide');
            $("#myModal5").modal('show');
            $("#ver5").click(function(){
              
              $("#myModal5").modal('hide');
              document.location = 'GP_TERCERO_PREDIO.php?id=<?php echo $id;?>';
              
            });
          }else{
             $("#myModalUpdate").modal('hide'); 
            $("#myModal6").modal('show');
            $("#ver6").click(function(){
              
              $("#myModal6").modal('hide');
              document.location = 'GP_TERCERO_PREDIO.php?id=<?php echo $id;?>';
              
            });
           
          }
        }
      });
    }

        </script> 
        
        <script type="text/javascript">
            $('#btnModifico').click(function(){
                document.location = 'GP_TERCERO_PREDIO.php?id=<?php echo $id;?>';
            });
        </script>
        <script type="text/javascript">
            $('#btnNoModifico').click(function(){
                document.location = 'GP_TERCERO_PREDIO.php?id=<?php echo $id;?>';
            });
        </script>
  </script>
  <script type="text/javascript">
      function modal()
      {
         $("#myModal").modal('show');
      }
  </script>
  
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'GP_TERCERO_PREDIO.php?id=<?php echo $id;?>';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'GP_TERCERO_PREDIO.php?id=<?php echo $id;?>';
      });
    
  </script>

</body>
</html>


