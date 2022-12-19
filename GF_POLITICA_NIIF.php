<?php
require_once ('Conexion/conexion.php');
require_once 'head_listar.php'; 
$id= $_GET['id'];
$bus = "SELECT id_unico, "
        . "nombre_politica, "
        . "descripcion, "
        . "norma_aplicable "
        . "FROM gf_politicas_contables "
        . "WHERE md5(id_unico)='$id' ";
$result= $mysqli->query($bus);
$row= mysqli_fetch_row($result);
$politica = ucwords(strtolower($row[1]));

#CUENTA UNO
$cuenta1= "SELECT id_unico, codi_cuenta, nombre FROM gf_cuenta ORDER BY codi_cuenta ASC";
$cuenta1 = $mysqli->query($cuenta1);
#CUENTA DOS
$cuenta2= "SELECT id_unico, codi_cuenta, nombre FROM gf_cuenta ORDER BY codi_cuenta DESC";
$cuenta2 = $mysqli->query($cuenta2);

#DEPENDENCIA
$dep = "SELECT id_unico, sigla, nombre FROM gf_dependencia ORDER BY sigla ASC";
$dep = $mysqli->query($dep);

#LISTAR
$listar = "SELECT
            ep.id_unico,
            ep.fecha_inicial,
            ep.fecha_final,
            ep.descripcion,
            ep.dependencia,
            d.nombre,
            d.sigla,
            ep.cuenta_uno,
            c1.codi_cuenta,
            c1.nombre,
            ep.cuenta_dos,
            c2.codi_cuenta,
            c2.nombre
          FROM
            gf_establecimiento_politicas_niif ep
          LEFT JOIN
            gf_dependencia d ON ep.dependencia = d.id_unico
          LEFT JOIN
            gf_cuenta c1 ON c1.id_unico = ep.cuenta_uno
          LEFT JOIN
            gf_cuenta c2 ON c2.id_unico = ep.cuenta_dos 
          WHERE ep.politica ='$row[0]'";
$listar = $mysqli->query($listar);
?>
<title>Establecimiento Política NIIF</title>
</head>
<body> 
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
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
label#fechaI-error, #fechaF-error, #descripcion-error, #dependencia-error, #cuenta1-error, #cuenta2-error{
    display: inline-block;
    color: #155180;
    font-weight: normal;
    font-style: italic;
    width: 200px;

}
body{
    font-size: 12px;
}

</style>
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
            changeMonth:true,
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
       
        
        $("#fechaI").datepicker().val(fecAct);
        $("#fechaF").datepicker().val(fecAct);
        
        
});
</script>
    <div class="container-fluid text-center">
	<div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top:-3px">Establecimiento Política NIIF</h2>
                <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Política: <?php echo $politica; ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GF_ESTABLECIMIENTOPJson.php">
                        <input type="hidden" id="politica" value="<?php echo $row[0]?>" name="politica">
                        <p align="center" style="margin-bottom: 25px; margin-top:0px; margin-left: 40px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        
                        <div class="form-group form-inline" style="margin-top:-10px; margin-left: 20px">
                            <div class="form-group form-inline " style="margin-left: 12px;">
                                <label for="fechaI" class="control-label" style="display:inline"><strong style="color:#03C1FB;">*</strong>Fecha Inicial :</label>
                                <input type="text" readonly="true" name="fechaI" id="fechaI" class="form-control" style="width:200px; display:inline" onchange="javaScript:fechaInicial();"/>
                            </div>
                            <div class="form-group form-inline " style="margin-left: 42px;">
                                <label for="fechaF" class="control-label" style="display:inline"><strong style="color:#03C1FB;">*</strong>Fecha Final :</label>
                                <input type="text" readonly="true" name="fechaF" id="fechaF" class="form-control" style="width:200px; display: inline"/>
                            </div>
                            <div class="form-group form-inline " style="margin-left: 40px;">
                                <label for="descripcion"class="control-label" style="width: 94px"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
                                <input type="text" name="descripcion" id="descripcion" class="form-control" style="width:200px; display:inline" placeholder="Descripción" title="Ingrese descripción"required/>
                            </div>
                            <div class="form-group form-inline " style="margin-left: 40px;">
                                <button type="submit" id="btnGuardar" class="btn btn-primary sombra" style="background: #00548F; color: #fff; border-color: #1075C1; margin:  0 auto;" title="Guardar" >
                                  <li class="glyphicon glyphicon-floppy-disk"></li>
                                </button>
                              </div>
                            
                        </div>
                        <div class="form-group form-inline" style="margin-top:-10px; margin-left: 20px">
                            <div class="form-group form-inline " style="margin-left: 5px;">
                                <label for="dependencia" class="control-label" style="width: 94px"><strong style="color:#03C1FB;">*</strong>Dependiencia :</label>
                                <select name="dependencia" id="dependencia" style="width: 200px" class="select2_single form-control" title="Seleccione dependencia" required  style="display: inline;">
                                    <option value="">Dependencia</option>
                                    <?php while($rowd = mysqli_fetch_row($dep)){?>
                                    <option value="<?php echo $rowd[0] ?>"><?php echo strtoupper($rowd[1]).' - '.ucwords(strtolower($rowd[2]));}?></option>;
                                </select>
                            </div>
                            <div class="form-group form-inline " style="margin-left: 26px;">
                                <label for="cuenta1" class="control-label" style="width: 94px"><strong style="color:#03C1FB;">*</strong>Cuenta Inicial:</label>
                                <select name="cuenta1" id="cuenta1" style="width: 200px" class="select2_single form-control" title="Seleccione cuenta inicial" required  style="display: inline;">
                                    <option value="">Cuenta Inicial</option>
                                    <?php while($row1 = mysqli_fetch_row($cuenta1)){?>
                                    <option value="<?php echo $row1[0] ?>"><?php echo ucwords((strtolower($row1[1].' - '.$row1[2])));}?></option>;
                                </select> 
                            </div>
                            <div class="form-group form-inline " style="margin-left: 40px;">
                                <label for="cuenta2" class="control-label" style="width: 94px"><strong style="color:#03C1FB;">*</strong>Cuenta Final:</label>
                                <select name="cuenta2" id="cuenta2"  style="width: 200px" class="select2_single form-control" title="Seleccione cuenta final" required  style="display: inline;">
                                    <option value="">Cuenta Final</option>
                                    <?php while($row2 = mysqli_fetch_row($cuenta2)){?>
                                    <option value="<?php echo $row2[0] ?>"><?php echo ucwords((strtolower($row2[1].' - '.$row2[2])));}?></option>;
                                </select> 
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
                                    <td><strong>Fecha Inicial</strong></td>
                                    <td><strong>Fecha Final</strong></td>
                                    <td><strong>Descripción</strong></td>
                                    <td><strong>Dependencia</strong></td>
                                    <td><strong>Cuenta Inicial</strong></td>
                                    <td><strong>Cuenta Final</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Fecha Inicial</th>
                                    <th>Fecha Final</th>
                                    <th>Descripción</th>
                                    <th>Dependencia</th>
                                    <th>Cuenta Inicial</th>
                                    <th>Cuenta Final</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while($rowl = mysqli_fetch_row($listar)){?>
                                <tr>
                                    <td style="display: none;"><?php echo $rowl[0]?></td>    
                                    <td><a  href="#" onclick="javascript:eliminar(<?php echo $rowl[0]?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a onclick="modificarModal(<?php echo $rowl[0].","."'".$rowl[1]."'".","."'".$rowl[2]."'".","."'".$rowl[3]."'".",".$rowl[4].",".$rowl[7].",".$rowl[10]?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td><?php echo date("d/m/Y", strtotime($rowl[1]));?></td>
                                    <td><?php echo date("d/m/Y", strtotime($rowl[2]));?></td>
                                    <td><?php echo ucwords(strtolower($rowl[3]));?></td>
                                    <td><?php echo $rowl[6].' - '.ucwords(strtolower($rowl[5]));?></td>  
                                    <td><?php echo $rowl[8].' - '.ucwords(strtolower($rowl[9]));?></td>
                                    <td><?php echo $rowl[11].' - '.ucwords(strtolower($rowl[12]));?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                         </table>
                    </div>
                </div>
            </div>
	</div>
    </div>
<script>
function fechaInicial(){
       var fechain= document.getElementById('fechaI').value;
       array_fechaI = fechain.split("/")
       var dI= array_fechaI[0];
       var mI= array_fechaI[1]-1;
       var aI= array_fechaI[2];
       var fechaIn= new Date(aI,mI,dI);
       var fechafi= document.getElementById('fechaF').value;
       array_fechaF = fechafi.split("/")
       var dF= array_fechaF[0];
       var mF= array_fechaF[1]-1;
       var aF= array_fechaF[2];
       var fechaFin= new Date(aF,mF,dF);
       if(fechaIn>fechaFin){
           document.getElementById('fechaF').value = fechain;
       }
            $( "#fechaF" ).datepicker( "destroy" );
            $( "#fechaF" ).datepicker({ minDate: fechain });
        
}
</script>
   <script src="js/select/select2.full.js"></script>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true,
      });
    });
  </script>
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de establecimiento política NIIF?</p>
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
        <?php #CUENTA UNO
                $cuenta1m= "SELECT id_unico, codi_cuenta, nombre FROM gf_cuenta ORDER BY codi_cuenta ASC";
                $cuenta1m = $mysqli->query($cuenta1m);
                #CUENTA DOS
                $cuenta2m= "SELECT id_unico, codi_cuenta, nombre FROM gf_cuenta ORDER BY codi_cuenta ASC";
                $cuenta2m = $mysqli->query($cuenta2m);

                #DEPENDENCIA
                $depm = "SELECT id_unico, sigla, nombre FROM gf_dependencia ORDER BY sigla ASC";
                $depm = $mysqli->query($depm);?>
      <div class="modal-body ">
          <form  name="formMod" id="formMod" method="POST" action="javascript:modificarItem()">
            <input type="hidden" name="idm" id="idm">
          <div class="form-group" style="margin-top: 13px;">
            <label for="fechaIm" class="control-label" style="width: 150px"><strong style="color:#03C1FB;">*</strong>Fecha Inicial :</label>
            <input type="text" readonly="true" name="fechaIm" id="fechaIm" class="form-control" style="width:250px; display:inline" onchange="javaScript:fechaInicialM();"/>                                
          </div> 
          <div class="form-group" style="margin-top: 13px;">
            <label for="fechaFm" class="control-label" style="width: 150px"><strong style="color:#03C1FB;">*</strong>Fecha Final :</label>
            <input type="text" readonly="true" name="fechaFm" id="fechaFm" class="form-control" style="width:250px; display:inline" />                                
          </div> 
          <div class="form-group" style="margin-top: 13px;">
            <label for="descripcionm"class="control-label" style="width: 150px"><strong style="color:#03C1FB;">*</strong>Descripción:</label>
            <input type="text" name="descripcionm" id="descripcionm" class="form-control" style="width:250px; display:inline" placeholder="Descripción" title="Ingrese descripción"required/>
          </div>
            <div class="form-group" style="margin-top: 13px;">
                <label for="dependenciam" class="control-label" style="width: 150px"><strong style="color:#03C1FB;">*</strong>Dependiencia :</label>
                <select name="dependenciam" id="dependenciam" style="width: 250px" class="select2_single form-control" title="Seleccione dependencia" required  style="display: inline;">
                    <?php while($rowdm = mysqli_fetch_row($depm)){?>
                    <option value="<?php echo $rowdm[0] ?>"><?php echo strtoupper($rowdm[1]).' - '.ucwords(strtolower($rowdm[2]));}?></option>;
                </select>
          </div>
            <div class="form-group" style="margin-top: 13px;">
            <label for="cuenta1m" class="control-label" style="width: 150px"><strong style="color:#03C1FB;">*</strong>Cuenta Inicial:</label>
                <select name="cuenta1m" id="cuenta1m" style="width: 250px" class="select2_single form-control" title="Seleccione cuenta inicial" required  style="display: inline;">
                    <?php while($row1m = mysqli_fetch_row($cuenta1m)){?>
                    <option value="<?php echo $row1m[0] ?>"><?php echo ucwords((strtolower($row1m[1].' - '.$row1m[2])));}?></option>;
                </select>
          </div>
            <div class="form-group" style="margin-top: 13px;">
                <label for="cuenta2m" class="control-label" style="width: 150px"><strong style="color:#03C1FB;">*</strong>Cuenta Final:</label>
                <select name="cuenta2m" id="cuenta2m"  style="width: 250px" class="select2_single form-control" title="Seleccione cuenta final" required  style="display: inline;">
                    <?php while($row2m = mysqli_fetch_row($cuenta2m)){?>
                    <option value="<?php echo $row2m[0] ?>"><?php echo ucwords((strtolower($row2m[1].' - '.$row2m[2])));}?></option>;
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
<script>
function fechaInicialM(){
       var fechain= document.getElementById('fechaIm').value;
       array_fechaI = fechain.split("/")
       var dI= array_fechaI[0];
       var mI= array_fechaI[1]-1;
       var aI= array_fechaI[2];
       var fechaIn= new Date(aI,mI,dI);
       var fechafi= document.getElementById('fechaFm').value;
       array_fechaF = fechafi.split("/")
       var dF= array_fechaF[0];
       var mF= array_fechaF[1]-1;
       var aF= array_fechaF[2];
       var fechaFin= new Date(aF,mF,dF);
       if(fechaIn>fechaFin){
           document.getElementById('fechaFm').value = fechain;
       }
            $( "#fechaFm" ).datepicker( "destroy" );
            $( "#fechaFm" ).datepicker({ minDate: fechain });
        
}
</script>
<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminar_GF_ESTABLECIMIENTOPJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true){
                      $("#myModal1").modal('show');
                      $("#ver1").click(function(){
                          document.location.reload();
                        });
                    } else {
                      $("#myModal2").modal('show');
                      $("#ver2").click(function(){
                          document.location.reload();
                        });
                  }}
              });
          });
      }
      function modificarModal(id,fechaI,fechaF, descripcion, dependencia, cuenta1, cuenta2){
          var fechaIn = fechaI.split("-").reverse().join("/");
          var fechaFi = fechaF.split("-").reverse().join("/");
            $("#idm").val(id);
            $("#fechaIm").datepicker().val(fechaIn);
            $("#fechaFm").datepicker().val(fechaFi);
            $("#descripcionm").val(descripcion);
            $("#dependenciam").val(dependencia);
            $("#cuenta1m").val(cuenta1);
            $("#cuenta2m").val(cuenta2);
            
            $("#myModalUpdate").modal('show');
          }
      
      function modificarItem()
    {
      var formData = new FormData($("#formMod")[0]);  
      $.ajax({
          
        type:"POST",
        url:"json/modificar_GF_ESTABLECIMIENTOPJson.php",
        data:formData,
        contentType: false,
         processData: false,
        success: function (data) {
          result = JSON.parse(data);
          if(result==true){
            $("#myModalUpdate").modal('hide');
            $("#myModal5").modal('show');
            $("#ver5").click(function(){
              
              $("#myModal5").modal('hide');
              document.location.reload();
              
            });
          }else{
             $("#myModalUpdate").modal('hide'); 
            $("#myModal6").modal('show');
            $("#ver6").click(function(){
              
              $("#myModal6").modal('hide');
              document.location.reload();
              
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
  

</body>
</html>


