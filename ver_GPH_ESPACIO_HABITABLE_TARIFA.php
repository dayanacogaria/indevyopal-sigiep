<?php
#

require_once ('head_listar.php');
require_once ('Conexion/conexion.php');
#session_start();
@$id = $_GET['id'];

$sql = "SELECT 
        ehta.id_unico, 
        ehc.id_espacio_habitable as id_ap,
        concat(ehap.codigo,' - ',ehap.descripcion) as des_ap,
        ehp.id_unico as id_piso,
        concat(ehp.codigo,' - ',ehp.descripcion) as des_piso,
        eht.id_unico as id_torre,
        concat(eht.codigo,' - ',eht.descripcion) as des_torre,
        ehc.id_concepto as id_concepto,
        cn.nombre,
        ehta.ano,
        ehta.valor,
        p.anno
        FROM gph_espacio_habitable_tarifa ehta
        left join gph_espacio_habitable_concepto ehc on ehc.id_unico= ehta.id_espacio_habitable_concepto
        left join gh_espacios_habitables ehap on ehap.id_unico=ehc.id_espacio_habitable
        left join gh_espacios_habitables ehp on ehp.id_unico=ehap.asociado
        left join gh_espacios_habitables eht on eht.id_unico=ehp.asociado
        left join gp_concepto cn on cn.id_unico=ehc.id_concepto
        left join gf_parametrizacion_anno p on p.id_unico=ehta.ano
        where md5(ehta.id_unico) = '$id' ";

$resultado = $mysqli->query($sql);
$res = mysqli_fetch_row($resultado);

?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
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
        var selected = '';   
        var fch=$('#sltFechaA').val();
        var tp=$('#sltTipo').val();
        var nct=$('#txtNumeroC').val();
        var abo=$('#txtAbono').val();
        var pc=$('#txtPorcentaje').val();
        var vlr=$('#txtValor').val();
        
        
        $('#form input[type=checkbox]').each(function(){
            if (this.checked) {
                selected += $(this).val()+',';
               
            }
        }); 
        window.location='json/registrarAcuerdoJSON.php?codigos='+selected+'&sltFechaA='+fch+
        '&sltTipo='+tp+'&txtNumeroC='+nct+'&txtAbono='+abo+'&txtPorcentaje='+pc+'&txtValor='+vlr;
        

        return false;
    });         
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
               
        
        $("#sltFecha").datepicker({changeMonth: true,}).val();
        
        
});
</script>

<script>
function estado(value){

     if(value=="1" ){

            document.getElementById("sltTipo").disabled=false;
            document.getElementById("sltContribuyente").disabled=true;
            

    }else{
            document.getElementById("sltContribuyente").disabled=false;
            document.getElementById("sltTipo").disabled=true;
}
}
</script>
   <title>Espacio Habitable Tarifa</title>
   <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Espacio Habitable Tarifa</h2>
                    <a href="<?php echo 'listar_GPH_ESPACIO_HABITABLE_TARIFA.php';?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:8px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords(("Datos"));?></h5>
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 10px">
                        
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
                                <!-------------------------------------------------------------------------------------- -->
                                <div class="form-group form-inline" style="margin-top:-25px">
                                    <!--Fecha-->

                                    <!----------Script para invocar Date Picker-->
                                    <script type="text/javascript">
                                       $(document).ready(function() {
                                            $("#datepicker").datepicker();
                                        });
                                    </script>
                                    <!--Apartamento-->
                                    <label for="Apartamento" class="col-sm-2 control-label">
                                        <strong class="obligado">*</strong>Espacio Habitable:
                                    </label>
                                    <input name="Apartamento" id="Apartamento" title="Ingrese Espacio Habitable" 
                                    type="text" style="width: 350px;height: 30px" class="form-control col-sm-1"  
                                        value="<?php echo $res[2]; ?>" readonly>  
                                    
                                    <!--Piso--->
                                    <label for="Piso" class="col-sm-1 control-label">
                                        <strong class="obligado">*</strong>Piso:
                                    </label>
                                    <input name="Piso" id="Piso" title="Ingrese Piso" 
                                    type="text" style="width: 270px;height: 30px" class="form-control col-sm-1"  
                                        value="<?php echo $res[4]; ?>" readonly>  
                                    
                                    <!--torre-->
                                    <label for="Torre" class="col-sm-2 control-label">
                                        <strong class="obligado">*</strong>Torre:
                                    </label>
                                    <input name="Torre" id="Torre" title="Ingrese Piso" 
                                    type="text" style="width: 350px;height: 30px" class="form-control col-sm-1"  
                                        value="<?php echo $res[6]; ?>" readonly>  

                                    
                                    <!--Concepto-->

                                    <label for="Concepto" class="col-sm-1 control-label">
                                        <strong class="obligado">*</strong>Concepto:
                                    </label>
                                    <input name="Concepto" id="Concepto" title="Ingrese Concepto" 
                                    type="text" style="width: 270px;height: 30px" class="form-control col-sm-1"  
                                        value="<?php echo $res[8]; ?>" readonly>  
                                
                                    <!--Año-->

                                    <label for="Ano" class="col-sm-2 control-label">
                                        <strong class="obligado">*</strong>Año:
                                    </label>
                                    <input name="Ano" id="Ano" title="Ingrese Ano" 
                                    type="text" style="width: 350px;height: 30px" class="form-control col-sm-1"  
                                        value="<?php echo $res[11]; ?>" readonly>  
                                   
                                    <!--Valor-->

                                    <label for="Valor" class="col-sm-1 control-label">
                                        <strong class="obligado">*</strong>Valor:
                                    </label>
                                    <input name="Valor" id="Valor" title="Ingrese Valor" 
                                    type="text" style="width: 270px;height: 30px" class="form-control col-sm-1"  
                                        value="<?php echo number_format($res[10], 2, '.', ','); ?>" readonly>  
                                </div>
                                
                                <!-- ----------------------------------------------------------------------  -->
                                                                                                      
                        
<!--------------------------------------------------------------------------------------------------- -->                              
                     
                       <!--<div class="form-group form-inline" style="margin-top:-5px">                            
                          
                           <label for="No" class="col-sm-2 control-label"></label>
                            <button id="enviar" type="submit" class="btn btn-primary sombra col-sm-1" 
                              style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: 800px ; ">
                                <li class="glyphicon glyphicon-floppy-disk"></li></button>                              
                        </div>-->
                    </form>
                          
                    
                   
<!---------------------------------------------------------------------------------------------------->                        
    
        <!-- </div> -->   
                
                    </div>
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
                                    <!--<a class="btn btn-primary btnInfo" href="registrar_GA_FACTURA_ACUERDO.php?nacuerdo=<?php echo $res[0] ?>&sltTiposelect=<?php echo $res[1] ?>">FACTURA ACUERDO</a>-->
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <!--<a class="btn btn-primary btnInfo" href="registrar_GN_CAUSA_RETIRO.php">CAUSA RETIRO</a>-->
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
          <p>¿Desea eliminar el registro seleccionado de Espacio habitable concepto?</p>
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
         $("#sltContribuyente").select2();
</script>
<script>
        function reportePdf(){
            $('form').attr('action', 'informes/INF_Certificado_Acuerdo_Pago.php');
            //$('form').attr('action', 'informes/INF_LIS_ACUERDOS_PAGO.php?nacuerdo=<?php echo $res[0] ?>&tipo=<?php echo $res[1] ?>');
            
        }
    </script>
</body>
</html>