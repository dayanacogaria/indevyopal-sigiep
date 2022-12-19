<?php
include 'head_listar.php';
include './Conexion/conexion.php';
?>

<?php 

$row="";
if(!empty($_GET['actualizacion_archivo'])){
    //id_unico de archivo de actualizacion
  
    $id_unico_encriptado=$_GET['actualizacion_archivo'];
    
    //campos archivo de actualizacion
    $sql_select="SELECT * FROM gs_actualizacion aa WHERE md5(aa.id_unico)='$id_unico_encriptado'";
    $resultado_select=$mysqli->query($sql_select);
    
    $row= mysqli_fetch_array($resultado_select);
    
    
    //actualizaciones del archivo de actualizacion 
  
}

?>


<title>Registrar  Actualización</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<!-- select2 -->
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script type="text/javascript">
/*Función para ejecutar el datapicker en en el campo fecha*/
$(function(){
    var fecha = new Date();
    var dia = fecha.getDate();
    var mes = fecha.getMonth() + 1;
    if(dia < 10){
        dia = "0" + dia;
    }
    if(mes < 10){
        mes = "0" + mes;
    }
    var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
    $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: 'Anterior',
        nextText: 'Siguiente',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
    $("#fecha").datepicker({changeMonth: true}).val();            
});
</script>
<style>
    .shadow {
        box-shadow: 1px 1px 1px 1px gray;
    }
    
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    dataTables_wrapper .ui-toolbar{padding:2px}
</style>
</head>
<body>
    <div class="container-fluid text-left">
        <div class="row content">
            <?php require 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 id="forma-titulo3" align="center" style="margin-top: 0px;">Registrar  Actualizaciones</h2>
                <a href="LISTAR_GS_ACTUALIZACION.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>


                  <h5 id="forma-titulo3a" align="center" style="width:94%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px;color:#0e315a">.</h5> 

                  <br><br>

                 <div class="client-form contenedorForma" style="margin-top:-7px;">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarActualizacionJsonn.php" style="margin-bottom:-10px;margin-left:10%;">
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                                Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                            </p>                        
                            <div class="form-group"> 
                                <input id="id_unico" hidden="true" value="<?php echo md5($row['id_unico']) ?>">
                                
                                <label for="fecha" class="col-sm-2 control-label">
                                  <strong class="obligado">*</strong>Fecha:
                                </label>     
                                <?php if(!empty($_GET['actualizacion_archivo'])){ 


                                        $fecha=$row['fecha'];
                                        $newDate = date("d/m/Y", strtotime($fecha));

                                    ?>   

                                    <input value="<?php echo $newDate  ?>" type="text" name="fecha" id="fecha" class="col-sm-1 col-md1-1 col-lg-1 form-control" style="width:23%;height:30px;" title="Ingrese la fecha" placeholder="Fecha" name="fecha" required> 
                               
                                <?php }else{ ?>

                                    <input value="<?php if(!empty($fecha)){$valorF = (String) $fecha;$fechaS = explode("-",$valorF); echo $fechaS[2].'/'.$fechaS[1].'/'.$fechaS[0];}else{echo date('d/m/Y');} ?>" type="text" name="fecha" id="fecha" class="col-sm-1 col-md1-1 col-lg-1 form-control" style="width:23%;height:30px;" title="Ingrese la fecha" placeholder="Fecha" name="fecha" required> 


                                <?php } ?>                          
                              
                                <label for="gestion" class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Gestión:
                                </label> 
                                <?php if(!empty($_GET['actualizacion_archivo'])){ ?>
                                      <input type="text" name="gestion" placeholder="Gestión" id="gestion" required="" class="col-sm-1 col-md1-1 col-lg-1 form-control " style="width: 23%" value="<?php echo $row['gestion'];  ?>">
                                <?php }else{ ?>
                                       <input type="text" name="gestion" placeholder="Gestión" id="gestion" required="" class="col-sm-1 col-md1-1 col-lg-1 form-control " style="width: 23%" value="">
                                <?php } ?>
                            </div>
                            
                            <div class="form-group" style="margin-top: -10px">
                                <label for="observaciones" class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Observaciones:
                                </label>    
                                
                                <?php if(!empty($_GET['actualizacion_archivo'])){ ?>
                                        <textarea name="observaciones" placeholder="Observaciones" id="observaciones" required="" class="form-control col-sm-1" rows="3" style="margin-top: 0px; width: 23%; height: 50px"><?php   echo $row['observaciones'];  ?></textarea>                                
                                <?php }else{ ?>
                                       <textarea name="observaciones" placeholder="Observaciones" id="observaciones" required="" class="form-control col-sm-1" rows="3" style="margin-top: 0px; width: 23%; height: 50px"></textarea>                                
                                <?php } ?>
                                
                                
                                
                                <label for="busc" class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Buscar:
                                </label>
                                       <select name="sltBuscar" id="sltBuscar" class="form-control col-sm-1 select2" style="width:23%;height:30px;" title="Seleccione para consultar comprobante" onchange="return buscar()">
                                  <?php 
                                    echo "<option value=''>Buscar Actualización</option>";
                                    ###########################################################################################################################
                                    # Consulta para datos de busqueda
                                    #
                                    ###########################################################################################################################
                                    $sqlCT = "SELECT id_unico,fecha,gestion FROM gs_actualizacion";
                                    $resultCT = $mysqli->query($sqlCT);
                                    ###########################################################################################################################
                                    # Impresión de valores
                                    #
                                    ###########################################################################################################################
                                    while ($rowCT = mysqli_fetch_row($resultCT)) {
                                      
                                      ###########################################################################################################################
                                      # Impresión de valores
                                      #
                                      ###########################################################################################################################
                                      echo "<option value=".$rowCT[0].">".$rowCT[1]." - ".ucwords(mb_strtolower($rowCT[2]))." "."</option>";
                                    }
                                   ?>
                                </select> 
                            </div>                           
                            <div class="form-group">
                                <div class="col-sm-3 col-sm-push-8" style="margin-top: -30px">       


                                    <a id="btnNuevo" onclick="javascript:nuevo()" class="btn btn-primary shadow" title="Ingresar nueva actualización"><li class="glyphicon glyphicon-plus"></li></a>                                                           
                                    <button type="submit" id="btnGuardar" class="btn btn-primary shadow" title="Guardar Actualización"><li class="glyphicon glyphicon-floppy-disk"></li></button>                                    
                                    <a id="btnModificar" onclick="javascript:ModificarActualizacion()" class="btn btn-primary shadow" title="Modificar Actualización"><li class="glyphicon glyphicon-pencil"></li></a>


                                    <a disabled="true" id="btnEliminar" onclick="javascript:eliminarActualizacion(<?php echo $row['id_unico']  ?>)" class="btn btn-primary shadow" title="Eliminar Actualización"><li class="glyphicon glyphicon-remove"></li></a>

                                    <?php
                                    if(!empty($_GET['actualizacion_archivo'])) {
                                        
                                        
                                        
                                        echo "<script>";
                                     
                                        echo "$('#btnEliminar').attr('disabled',false)";
                                        echo "</script>";
                                    }
                                    ?>
                                </div>                                                                       
                            </div>                        
                        </form>
                    </div>                                                   
                </div>
            <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 5px">   
                <form name="formDetalle" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarDetalleActualizacionJson.php" style="margin-bottom:-10px">
                    <input hidden="true" name="id_archivo" value="<?php echo $row['id_unico']; ?>">
                    <div class="form-group" style="margin-left: 10%;margin-top: 1%;">
                        <label for="ruta" class="col-sm-1 control-label">
                              <strong class="obligado">*</strong>Ruta:
                        </label>
                        <input type="file" name="txtRuta" value="" class="form-control col-sm-1" style="width: 35%" required="" />
                               <label for="observaciones" class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Observaciones:
                                </label>    
                                
                          
                             <textarea name="observacionesDetalleActualizacion" placeholder="Observaciones" id="observaciones" required="" class="form-control col-sm-1" rows="3" style="margin-top: 0px; width: 23%; height: 50px;resize: none"><?php  ?></textarea>                                
                              
                        <div class="col-sm-1">
                            <button  disabled="true" type="submit" id="btnGuardarDetalle" class="btn btn-primary shadow" title="Guardar Detalle"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                            <?php
                            if(!empty($_GET['actualizacion_archivo'])) {
                                
                                
                                
                                echo "<script>";
                                echo "$('#btnGuardar').attr('disabled',true);";
                                echo "$('#btnGuardarDetalle').attr('disabled',false)";
                                echo "</script>";
                            }
                            ?>
                        </div>                                                        
                    </div>                                                                                                                             
                </form>
            </div>
            <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 5px"> 
                <div class="table-responsive contTabla" >
                    <div class="table-responsive contTabla" >
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="oculto" ></td>
                                    <td width="7%" class="cabeza"></td>
                                    <td class="cabeza"><strong>Observaciones</strong></td>
                                    <td class="cabeza"><strong>Descargar</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto"></th>
                                    <th class="cabeza"></th>
                                    <th class="cabeza"> </th>
                                    <th class="oculto"> </th>
                                </tr>
                            </thead>
                            <tbody>
                          <?php 
                            if(!empty($_GET['actualizacion_archivo'])){
                                $sqlA = "SELECT id_unico,observaciones,ruta FROM gs_actualizacion_archivo a WHERE md5(a.id_actualizacion)='$id_unico_encriptado'";
                                $rsA = $mysqli->query($sqlA);
                                while ($rowA = mysqli_fetch_row($rsA)) { ?>
                                     <tr>
                                      

                                            <td class="oculto"></td>
                                            <td> <a  href="#" onclick="javascript:eliminarDetalleActualizacion(<?php echo $rowA[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a></td>
                                            <td><?php echo $rowA[1] ?></td>
                                         <td><center><a href="<?php echo substr($rowA[2],3,strlen($rowA[2])) ?>" download="" class="glyphicon glyphicon-download-alt"></a></center></td>

                                    </tr>                          
                             <?php   } 
                            } ?>
                            </tbody> 
                       </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <div class="modal fade" id="myModalEliminarActualizacion" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar la Actualización?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
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
          <p>¿Desea eliminar el Detalle de Actualización?</p>
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
          <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>   
    
    
            <?php require 'footer.php'; ?>
        </div>        
    </div>
      <!-- Modales de modificado -->
        <div class="modal fade" id="infoM" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información modificada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="noModifico" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">          
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se ha podido modificar la información.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

 
  <script type="text/javascript">
      function modal()
      {
         $("#myModal").modal('show');
      }
  </script>
  
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'GS_REGISTRO_ACTUALIZACION.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'GS_REGISTRO_ACTUALIZACION.php';
      });
    
  </script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="js/md5.js"></script>
    <script>
        $(".select2").select2();
        
        function ModificarActualizacion(){           
            var id_unico=$("#id_unico").val();

            var fecha=$("#fecha").val();
            var gestion=$("#gestion").val();
            var observaciones=$("#observaciones").val();
           
            var form_data={   
                id_unico:id_unico,             
                fecha:fecha,
                gestion:gestion,
                observaciones:observaciones,
                action:'modificar'
            }; 
           
            var result = ' ';
            $.ajax({
                type: 'POST',
                url: "json/modificarActualizacionJson.php",
                data: form_data,
                success: function (data) {                    
                    result = JSON.parse(data);
                    if (result==true) {
                        $("#infoM").modal('show');
                    }else{
                        $("#noModifico").modal('show');
                    }                    
                }
            }).error(function(data, textStatus, errorThrown){
                console.log('data :'+data+' ,Estado:'+textStatus+', Error:'+errorThrown);
            });                              
        }
        
        function nuevo(){      
            window.location = 'GS_REGISTRO_ACTUALIZACION.php';
        }

        function eliminarActualizacion(id){
           var result = '';
           $("#myModalEliminarActualizacion").modal('show');
           $("#aceptar").click(function(){
            $("#myModalEliminarActualizacion").modal('hide');
            $.ajax({
              type:"GET",
              url:"json/eliminarActualizacionJson2.php?id="+id,
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
        
        function buscar() {
            var id = $("#sltBuscar").val();
            if(id.length > 0) {
                window.location = 'GS_REGISTRO_ACTUALIZACION.php?actualizacion_archivo='+md5(id);
            }
        }
     
    </script>
   <script type="text/javascript">
      function eliminarDetalleActualizacion(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarActualizacionJson.php?id="+id,
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