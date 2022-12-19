<?php 
##################################MODIFICACIONES########################################
#20/02/2018 |Erica G.|Opcion Balance Final
#23/06/2017 |Erica G.|OPCION POR DIGITOS CUENTA, PARAMETRIZACION ANNO 
#07/03/2017 |Erica G |OPCION EXCEL
#04/03/2017 |Erica G |Valor opcion combos DISEÑO
##########################################################################################
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$annio = $_SESSION['anno'];
$compania = $_SESSION['compania'];
?>
<title>Balance de Prueba</title>
</head>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<!--######VALIDACIONES#####-->
<style>
    label #sltAnnio-error, #sltmesi-error, #sltmesf-error, #sltcodi-error, #sltcodf-error {
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;
}
body{
    font-size: 12px;
}
</style>
<script>

$().ready(function() {
  var validator = $("#form").validate({
        ignore: "",
     
    errorPlacement: function(error, element) {
      
      $( element )
        .closest( "form" )
          .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    }
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<style>
    .form-control {font-size: 12px;}
</style>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left" style="margin-top: -20px"> 
                <h2 align="center" class="tituloform">Balance de Prueba</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                        <input type="hidden" name="tituloH" value="BALANCE DE PRUEBA">
                        <input type="hidden" name="outputF" value="Informe_Balance_Prueba">
                        <div class="form-group">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltAnnio" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Año:</label>
                            <select name="sltAnnio" id="sltAnnio" class="select2_single form-control" title="Seleccione Año" style="height: auto " required>
                                <option value="">Año</option>
                                <?php 
                                $annio = "SELECT  id_unico, anno FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC";
                                $rsannio = $mysqli->query($annio);
                                while ($filaAnnio = mysqli_fetch_row($rsannio)) { ?>
                                     <option value="<?php echo $filaAnnio[0];?>"><?php echo $filaAnnio[1];?></option>                                
                                <?php }?>                                    
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltmesi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes Inicial:</label>
                            <select required name="sltmesi" id="sltmesi" style="height: auto" class="select2_single form-control" title="Mes Inicial" >
                                <option value="">Mes Inicial</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltmesf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Mes Final:</label>
                            <select required name="sltmesf" id="sltmesf" style=" height: auto" class="select2_single form-control" title="Mes Final" >
                                   <option value="">Mes Final</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltcodi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Inicial:</label>
                            <select required name="sltcodi" id="sltcodi" style="height: auto" class="select2_single form-control" title="Seleccione Cuenta Inicial">
                                <option value="">Código Inicial</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltcodf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Final:</label>
                            <select required name="sltcodf" id="sltcodf" style="height: auto" class="select2_single form-control" title="Seleccione Cuenta Final">
                                <option value="">Código Final</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sltInforme" class="control-label col-sm-5">Informe por:</label>
                            <select name="sltInforme" id="sltInforme" class="form-control select2_single">
                                <option value="0">Informe</option>              
                                <option value="1">Balance CGN</option>
                                <option value="2">Balance Con Terceros</option>
                                <option value="3">Niveles</option>
                                <option value="4">Balance Final</option>
                            </select>
                        </div> 
                        <div class="form-group" id="digitos" style="display:none" >
                            <label for="sltInforme" class="control-label col-sm-5">Número de dígitos:</label>
                            <input type="text" name="ndigitos" id="ndigitos" class="form-control">
                        </div>
                        <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                            <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                            <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                            <button style="margin-left:10px;" id="btnTxt" name="btnTxt" onclick="reportePlan()" class="btn sombra btn-primary" title="Generar archivo plano"><i class="fa fa-file-text-o" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </form>
            </div>    
        </div>
    </div>
    <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>    
    $("#sltAnnio").change(function(){
        
       var form_data={action: 1, annio :$("#sltAnnio").val()};
       var optionMI ="<option value=''>Mes Inicial</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesCnt.php',
          data: form_data,
          success: function(response){
              optionMI =optionMI+response;
              $("#sltmesi").html(optionMI).focus();              
          }
       });
       var form_data={action: 2, annio :$("#sltAnnio").val()};
       var optionMF ="<option value=''>Mes Final</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesCnt.php',
          data: form_data,
          success: function(response){
              optionMF =optionMF+response;
              $("#sltmesf").html(optionMF).focus();              
          }
       });
       var form_data={action: 3, annio :$("#sltAnnio").val()};
       var optionCI ="<option value=''>Código Inicial</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesCnt.php',
          data: form_data,
          success: function(response){
              optionCI =optionCI+response;
              $("#sltcodi").html(optionCI).focus();              
          }
       });
       var form_data={action: 4, annio :$("#sltAnnio").val()};
       var optionCF ="<option value=''>Código Final</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesCnt.php',
          data: form_data,
          success: function(response){
              optionCF =optionCF+response;
              $("#sltcodf").html(optionCF).focus();              
          }
       });
       
       
    });
</script>
<script src="js/select/select2.full.js"></script>
<script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
      $("#btnTxt").attr('disabled',true);
    });
</script>
<?php require_once 'footer.php' ?>  
<script>
function reporteExcel(){
  var opcion = document.getElementById('sltInforme').value;
  switch(opcion){
    case('0'):
      $('form').attr('action', 'informes/generar_INF_BALANCE_PRUEBAEXCEL.php');
    break;
    case('1'):
      $('form').attr('action', 'informes/generar_INF_BALANCE_CGN.php?window=excel');
    break;
    case('2'):
      $('form').attr('action', 'informes/generar_INF_BALANCE_PRUEBA_TERCEROS_EXCEL.php');
    break;
    case('3'):
      var dig = $("#ndigitos").val();  
      $('form').attr('action', 'informes/generar_INF_BALANCE_PRUEBAEXCEL.php?digitos='+dig);
    break;
    case('4'):
      $('form').attr('action', 'informes/generar_INF_BALANCE_FINAL.php?t=2');
      //$('form').attr('action', 'informes/generar_INF_BALANCE_PRUEBA_COMPANIA.php');
    break;
  }   
}

function reportePdf(){
  var opcion = document.getElementById('sltInforme').value;
  switch(opcion){
    case('0'):
      $('form').attr('action', 'informes/generar_INF_BALANCE_PRUEBA.php');
    break;
    case('1'):
      $('form').attr('action', 'informes/generar_INF_BALANCE_CGN.php?window=pdf');
    break;
    case('2'):
      $('form').attr('action', 'informes/generar_INF_BALANCE_PRUEBA_TERCEROS.php');
    break;
    case('3'):
      var dig = $("#ndigitos").val();  
      $('form').attr('action', 'informes/generar_INF_BALANCE_PRUEBA.php?digitos='+dig);
    break;
    case('4'):
      $('form').attr('action', 'informes/generar_INF_BALANCE_FINAL.php?t=1');
    break;
  }  
}

function reportePlan() {
  $('form').attr('action', 'informes/generar_INF_BALANCE_CGN.php?window=txt');
}

$("#sltInforme").change(function(){
  var opcion = parseInt($("#sltInforme").val());
  if(opcion === 1){
    $("#btnTxt").attr('disabled',false);
    $("#digitos").css('display','none');
  }else{
    if(opcion ==3){  
        $("#digitos").css('display','block');
        $("#btnTxt").attr('disabled',true);
    } else{
        if(opcion==4){
            $("#btnTxt").attr('disabled',true);
            $("#digitos").css('display','none');
            $("#sltmesi").attr('required',false);
            $("#sltmesf").attr('required',false);
            $("#sltcodi").attr('required',false);
            $("#sltcodf").attr('required',false);
        } else {
            $("#btnTxt").attr('disabled',true);
            $("#digitos").css('display','none');
        }
    }
  }
});

$("#sltmesi").change(function(){
    let mesf = parseFloat($("#sltmesf").val());
    let mesi = parseFloat($("#sltmesi").val());
    if(mesf!=''){
        if(mesi>mesf){
            $("#mensaje").html('Mes Inicial Mayor que el Mes Final');
            $("#modalMensajes").modal("show");
            $("#Aceptar").click(function(){
                document.location.reload();
            })
            
        }
    }
})
$("#sltmesf").change(function(){
    let mesf = parseFloat($("#sltmesf").val());
    let mesi = parseFloat($("#sltmesi").val());
    if(mesi!=''){
        if(mesi>mesf){
            $("#mensaje").html('Mes Inicial Mayor que el Mes Final');
            $("#modalMensajes").modal("show");
            $("#Aceptar").click(function(){
                document.location.reload();
            })
        }
    }
})
</script>
</body>
</html>