<?php
include 'head_listar.php';
include './Conexion/conexion.php';

?>
<title>Actualizaciones</title>
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
                 <h2 id="forma-titulo3" align="center" style="margin-top: 0px;">Actualizaciones</h2>

               </div>
            
                    <div class="col-sm-10 col-md-10 col-lg-10" style="margin-top: 5px"> 
                        <div class="table-responsive contTabla" >
                            <div class="table-responsive contTabla" >
                                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <td class="oculto" ></td>
                                            <td width="7%" class="cabeza"></td>
                                            <td class="cabeza"><strong>Fecha</strong></td>
                                            <td class="cabeza"><strong>Gestión</strong></td>
                                            <td class="cabeza"><strong>Observaciones</strong></td>
                                            <td class="cabeza"><strong>Ver</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="oculto"></th>
                                            <th class="cabeza"></th>
                                            <th class="cabeza"> </th>
                                            <th class="cabeza"> </th>
                                            <th class="cabeza"> </th>
                                            <th class="oculto"> </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql="SELECT aa.*,DATE_FORMAT(aa.fecha,'%d-%m-%Y') AS fechaConvertida FROM gs_actualizacion aa ORDER BY fecha DESC";
                                        $resultado=$mysqli->query($sql);
                                        while($row= mysqli_fetch_array($resultado)){ ?>
                                        <tr>
                                            
                                                <td class="oculto"></td>
                                                <td> </td>
                                                <td><?php echo $row['fechaConvertida'] ?></td>
                                                <td><?php echo $row['gestion'] ?></td>
                                                <td><?php echo $row['observaciones'] ?></td>
                                                <td><center><a title="Ver detalles" style="text-decoration: none" class="glyphicon glyphicon-eye-open" onclick="javascript:open_modal_r(<?php echo $row['id_unico']  ?>)"></a></center></td>
                                                <!--<td class="text-center"><a class="glyphicon glyphicon-download-alt" href="<?php echo substr($row[4], 3, strlen($row[4])) ?>" download=""></a></td><!--download-->
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div align="right"><a href="GS_REGISTRO_ACTUALIZACION.php" class="btn btn-primary" style="box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       
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
          <p>¿Desea eliminar el registro de Actualización?</p>
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
    

    
 

  
    <script type="text/javascript" src="js/select2.js"></script>
    <script>
        $(".select2").select2();
    </script>
 <script type="text/javascript">
      function modal()
      {
         $("#myModal").modal('show');
      }
  </script>
  
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'listar_GS_ACTUALIZACION.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'listar_GS_ACTUALIZACION.php';
      });
    
  </script>
  <script type="text/javascript" >
    function enviarIdActualizacion(id){   
            
         window.location='?idActualizacion='+id;
        
    }                                                                                        
</script>
<script>
        function open_modal_r(id) {  
              
             
            var form_data={                            
              id:id 
            };
             $.ajax({
                type: 'POST',
                url: "GS_ACTUALIZACIONES_MODAL.php#mdlModificarReteciones",
                data:form_data,
                success: function (data) { 
                    $("#mdlModificarReteciones").html(data);
                    $(".movi").modal("show");
                }
            }).error(function(data,textStatus,jqXHR){
                alert('data:'+data+'- estado:'+textStatus+'- jqXHR:'+jqXHR);
            })              
        }
    </script>

<?php require 'GS_ACTUALIZACIONES_MODAL.php' ; ?>
</body>
</html>