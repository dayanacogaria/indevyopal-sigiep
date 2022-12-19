<?php
#

require_once ('head_listar.php');
require_once ('Conexion/conexion.php');
require_once 'funciones/funcionLiquidador.php';
#session_start();
@$id = $_GET['idE'];
$anno = $_SESSION['anno'];
//


@$array = array (); 

if(empty($disp)){     
     $a = "none";
} else {
    $a="inline-block";
}
if(empty($nacuerdo)){     
     $a2 = "none";
} else {
    $a2="inline-block";
}


?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3.js" charset="utf-8"></script>

<link rel="stylesheet" href="css/autosuggest_inquisitor.css" type="text/css" media="screen" charset="utf-8" />
<style >
   table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
   table.dataTable tbody td,table.dataTable tbody td{padding:1px}
   .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
       font-family: Arial;}
</style>


<script type="text/javascript" src="../jquery.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#enviar').click(function(){
        var selected_ap = '';
        var princ = document.getElementById("checks_prin");
        var tabla1 = document.getElementById("tableO");
        var elemtabla1 = tabla1.getElementsByTagName("input");
        var con=$('#sltConcepto').val();
        var tip=$('#sltTipo').val();
        
        var i=0;
        
            for (i = 0; i < elemtabla1.length; i++) {
                if (elemtabla1[i].type == 'checkbox')
                        if (elemtabla1[i].checked == true) {
                            selected_ap += elemtabla1[i].value+',';
                            
                        }                        
            }
            
            
        if(selected_ap === ''|| con===''||tip===''){
             $("#myModalcomp").modal('show');
        }else{
            
            window.location='json/registrar_GN_Concepto_BaseJSON.php?id_ap='+selected_ap
                    +'&id_con='+con+'&tipo='+tip+'&opcion=R';
            
        }
        
        return false;
    });         
});  
</script>
 <script>
        $(document).ready(function() {
            var i= 0;
            $('#tableO thead th').each( function () {
                if(i => 0) {
                    var title = $(this).text();
                    switch (i){
                        case 0:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 1:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 2:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 3:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 4:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 5:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                    }
                    i = i+1;
                } else {
                    i = i+1;
                }
            });
            
            var i2= 0;
            $('#tableO2 thead th').each( function () {
                if(i2 => 0) {
                    var title = $(this).text();
                    switch (i2){
                        case 0:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 1:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 2:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 3:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 4:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 5:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                    }
                    i2 = i2+1;
                } else {
                    i2 = i2+1;
                }
            });
            
            
            
            // DataTable
            var table = $('#tableO').DataTable({
                "autoFill": true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                },
                scrollY: 120,
                "scrollX": true,
                scrollCollapse: true,
                paging: false,
                fixedColumns:   {
                    leftColumns: 1
                },
                'columnDefs': [{
                    'targets': 0,
                    'searchable':false,
                    'orderable':false,
                    'className': 'dt-body-center'
                }]
            });
            
            // DataTable
            var table2 = $('#tableO2').DataTable({
                "autoFill": true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                },
                scrollY: 120,
                "scrollX": true,
                scrollCollapse: true,
                paging: false,
                fixedColumns:   {
                    leftColumns: 1
                },
                'columnDefs': [{
                    'targets': 0,
                    'searchable':false,
                    'orderable':false,
                    'className': 'dt-body-center'
                }]
            });
            
            
            
            var i = 0;
            table.columns().every( function () {
                var that = this;
                if(i!=0) {
                    $( 'input', this.header() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                    i = i+1;
                } else {
                    i = i+1;
                }
            });
            
            var i2 = 0;
            table2.columns().every( function () {
                var that = this;
                if(i2!=0) {
                    $( 'input', this.header() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                    i2 = i2+1;
                } else {
                    i2 = i2+1;
                }
            });
        });
    </script>
<script type="text/javascript">
        $(function(){
            var hoy2 = new Date();            
            var mm = hoy2.getMonth()+1; //hoy es 0!
            var yyyy = hoy2.getFullYear();
            var ultimoDia = new Date(yyyy, mm, 0);
            var ultd=ultimoDia.getDate();
            var dd = hoy2.getDate();
           
            var dias= ultd-dd;
            var xxx='+'+dias+'d';
            //console.info(" ultimo dia "+ultd+"   dias transcurridos  "+dd)
            //console.info(" valor del la resta compuesta "+xxx);
            $( "#sltFechaA" ).datepicker({ maxDate: xxx});
        });
    </script>

<script src="js/jquery-ui.js"></script>

<script>

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
            yearSuffix: '',
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
               
        $("#sltFechaA").datepicker({changeMonth: true,}).val();
        $("#sltFecha").datepicker({changeMonth: true,}).val();
        
        
});
</script>

<script>
function estado(value){

     if(value=="1" ){

            document.getElementById("sltPerfil").disabled=false;
            document.getElementById("sltTercero").disabled=true;
            

    }else{
            document.getElementById("sltTercero").disabled=false;
            document.getElementById("sltPerfil").disabled=true;
}
}
</script>
   <title>Registrar Concepto Base</title>
   <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 col-md-10 col-lg-10 text-left" style="margin-top: 0px">
                      <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Concepto Base</h2>
                      
                      <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 10px">
                          <form id="formid" name="formid" class="form-horizontal"  enctype="multipart/form-data" >
                              <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
<!-------------------------------------------------------------------------------------------------------------------- -->
                          <!-- ----------------------------------------------------------------------  -->
                          <div class="form-group form-inline" style="margin-top:5px; ">
                            <?php require_once './menu.php'; 
                            $tab_fd= '';
                                
                            ?>
                         
                              <!--concpeto-->
                                    
                                    <?php  

                                    
                                        $tip = "SELECT id_unico, concat(descripcion,' (',codigo,')') nomb from gn_concepto order by codigo asc";
                                        $t[0]="";
                                        $t[1]="Concepto";
                                    
                                          $tipon = $mysqli->query($tip);
                                      ?> 
                                      <label for="sltConcepto" class="col-sm-2 control-label">
                                          <strong class="obligado">*</strong>Concepto Aplicar:
                                      </label>
                                      <select   name="sltConcepto" id="sltConcepto" title="Seleccione Concepto" 
                                                style="width: 380px;height: 30px" class="form-control col-sm-3" required>
                                          <option value="<?php echo $t[0]; ?>"><?php echo $t[1]; ?></option>

                                         <?php 
                                              while($rowEV = mysqli_fetch_row($tipon))
                                              {
                                                  echo "<option  value=".$rowEV[0].">".$rowEV[1]."</option >";
                                              }

                                          ?>                                                       
                                      </select>
                                   
                              
                              <!--tipo-->
                                    <?php  

                                    
                                        $tip = "SELECT * from gn_tipo_base order by nombre asc";
                                        $t[0]="";
                                        $t[1]="Tipo Base";
                                    
                                          $tipon = $mysqli->query($tip);
                                      ?> 
                                      <label for="sltTipo" class="col-sm-2 control-label">
                                          <strong class="obligado">*</strong>Tipo Base:
                                      </label>
                                      <select   name="sltTipo" id="sltTipo" title="Seleccione Tipo Base" 
                                                style="width: 240px;height: 30px" class="form-control col-sm-2" required>
                                          <option value="<?php echo $t[0]; ?>"><?php echo $t[1]; ?></option>

                                         <?php 
                                              while($rowEV = mysqli_fetch_row($tipon))
                                              {
                                                  echo "<option  value=".$rowEV[0].">".$rowEV[1]."</option >";
                                              }

                                          ?>                                                       
                                      </select>
                         </div>
                         

                    <!-- <div class="col-sm-12 text-left" style="display:<?php echo $a?>">-->
                          <div class="form-group form-inline" >
                          <strong style=" font-size: 12px; margin-left: 52px; margin-right: 5px;margin-top:5px; margin-bottom: 10px;">CONCEPTOS</strong><input style="margin-left: 460px; margin-right: 5px;margin-top:10px; margin-bottom: 10px;" type="checkbox" 
                                 onclick="if (this.checked) marcar(true); else marcar(false);" /><strong style=" font-size: 12px;">Marcar/Desmarcar Todos</strong>
                              <script type="text/javascript">
                                        function marcar(status) 
                                        {
                                           var tabla1 = document.getElementById("tableO");
                                           var eleNodelist1 = tabla1.getElementsByTagName("input");
                                           
                                            for (i = 0; i < eleNodelist1.length; i++) {
                                                if (eleNodelist1[i].type == 'checkbox')
                                                    if (status == null) {
                                                        eleNodelist1[i].checked = !eleNodelist1[i].checked;
                                                    }
                                                    else eleNodelist1[i].checked = status;
                                            }
                                        }
                                </script>
                                <div class="table-responsive" style="margin-left: 50px; margin-right: 50px;margin-top:0px;">
                                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                                        <table id="tableO" class="table table-striped table-condensed display" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <td style="display: none;">Identificador</td>
                                                    <td width="7%" class="cabeza"></td>                                        
                                                    <!-- Actualización 24 / 02 16:43 No es necesario mostrar el nombre del empleado
                                                    <td class="cabeza"><strong>Empleado</strong></td>
                                                    -->
                                                    <td class="cabeza"><strong>Concepto</strong></td>
                                                    <td class="cabeza"><strong>Seleccione</strong></td>
                                                    
                                                </tr>
                                                <tr>
                                                    <th class="cabeza" style="display: none;">Identificador</th>
                                                    <th class="cabeza" width="7%"></th>
                                                    <!-- Actualización 24 / 02 16:43 No es necesario mostrar el nombre del empleado
                                                    <th class="cabeza">Empleado</th>
                                                    -->
                                                    <th class="cabeza">Concepto</th>
                                                    <th class="cabeza">Seleccione</th>                                                   
                                                    
                                                </tr>
                                            </thead>    
                                            <tbody>
                                                <?php 
                                               
                                                 $sql1="SELECT id_unico, concat(descripcion,' (',codigo,')') nomb from gn_concepto order by codigo asc";
                                                
                                                  $re = $mysqli->query($sql1);
                                                while ($rowC = mysqli_fetch_row($re)) {  
                                                        $id_un = $rowC[0];
                                                        $n_con = $rowC[1];
                                                        
                                                        ?>
                                                 <tr>
                                                    <td style="display: none;"><?php echo $row[0]?></td>
                                                    <td>
                                                        
                                                    </td>                                        
                                                    <td class="campos text-left"><?php echo $n_con;?></td>
                                                    <td class="campos text-center">
                                                        <input name="cod" id="sel" type="checkbox" value="<?php echo $id_un ?>">
                                                    </td> 
                                                    
                                                </tr> 
                                                <?php }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                          </div>
                              
<!--------------------------------------------------------------------------------------------------- -->                              
                      
                        <div class="form-group form-inline" style="margin-top:-5px">                            
                          
                           <!-- <label for="No" class="col-sm-2 control-label"></label>-->
                            <button id="enviar" type="submit" class="btn btn-primary sombra col-sm-1" 
                              style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: 1020px ; ">
                                <li class="glyphicon glyphicon-floppy-disk"></li></button>                              
                        </div>
                    </form>
                          
                       
                  </div>

                   
<!---------------------------------------------------------------------------------------------------->                        
    
        <!-- </div> -->   
                
            </div>
            <div class="col-sm-8 col-sm-2" style="margin-top:-22px">
                <table class="tablaC table-condensed text-center" align="center">
                        <thead>
                            <tr>
                                <tr>                                        
                                    <th>
                                        <!--<h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>-->
                                    </th>
                                </tr>
                        </thead>
                        <tbody>
                            <tr>                                    
                                <td>
                                    <!--<a class="btn btn-primary btnInfo" href="registrar_GN_EMPLEADO.php">RECAUDO ACUERDO</a>-->
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                   <!--- <a class="btn btn-primary btnInfo" href="registrar_GN_CAUSA_RETIRO.php">CAUSA RETIRO</a>-->
                                </td>
                            </tr>                                                        
                            <!--<tr>   
                            no es necesario mostrar el estado porque solo pueden ser dos vinculacion retiro                                 
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_ESTADO_VINCULACION_RETIRO.php">ESTADO</a>
                                </td>
                            </tr>-->                                                        
                            <tr>                                    
                                <td>
                                    <!--<a class="btn btn-primary btnInfo" href="registrar_GN_TIPO_VINCULACION.php">TIPO VINCULACION</a>-->
                                </td>
                            </tr>
                </table>
          </div>
      </div>                                    
    </div>
   <div>
<?php require_once './footer.php'; ?>
        <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Espacio habitable tercero?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal1" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información eliminada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" onclick="recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
   <div class="modal fade" id="myModalcomp" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Asegurese que los campos obligatorios esten diligenciados.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver8"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
          <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>


  <!--Script que dan estilo al formulario-->

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
<!--Scrip que envia los datos para la eliminación-->
<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarVinculacionRetiroJson.php?id="+id,
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

  <script type="text/javascript">
      function modal()
      {
         $("#myModal").modal('show');
      }
  </script>
<script type="text/javascript">
      function recargar()
      {
        window.location.reload();     
      }
  </script>     
    <!--Actualiza la página-->
  <script type="text/javascript">
    
      $('#ver1').click(function(){ 
         reload();
        //window.location= '../registrar_GN_ACCIDENTE.php?idE=<?php #echo md5($_POST['sltEmpleado'])?>';
        //window.location='../listar_GN_ACCIDENTE.php';
        window.history.go(-1);        
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        window.history.go(-1);
      });    
  </script>
</div>
<script>
function fechaInicial(){
        var fechain= document.getElementById('sltFechaA').value;
        var fechafi= document.getElementById('sltFecha').value;
          var fi = document.getElementById("sltFecha");
        fi.disabled=false;
      
       
            $( "#sltFecha" ).datepicker( "destroy" );
            $( "#sltFecha" ).datepicker({ changeMonth: true, minDate: fechain});
     
}
</script>
<script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
         $("#sltVinculacion").select2();
</script>

<script type="text/javascript" src="js/select2.js"> </script>
        <script type="text/javascript"> 
         $("#sltCausa").select2();
</script>
<script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
         $("#sltTipo").select2();
         $("#sltConcepto").select2();
         
         
</script>
</body>
</html>