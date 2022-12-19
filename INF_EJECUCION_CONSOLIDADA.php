<?php 
require_once('Conexion/conexion.php'); 
require_once('Conexion/ConexionPDO.php'); 
require_once 'head.php'; 
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];

if($_REQUEST['t']==1){
    $t      = 'Ejecución Presupuestal Consolidada de Gastos';
    $rowri  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 7 ORDER BY codi_presupuesto ASC");
    $rowrf  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 7 ORDER BY codi_presupuesto DESC");
    $action = 1 ;
}elseif($_REQUEST['t']==2){
    $t      = 'Ejecución Presupuestal Consolidada de Ingresos';
    $rowri  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 6 ORDER BY codi_presupuesto ASC");
    $rowrf  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 6 ORDER BY codi_presupuesto DESC");
    $action = 2 ;
}elseif($_REQUEST['t']==3){
    $t      = 'Ejecución Presupuestal Consolidada Gerencial de Gastos Por Fuente';
    $rowri  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 7 ORDER BY codi_presupuesto ASC");
    $rowrf  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 7 ORDER BY codi_presupuesto DESC");
    $action = 3 ;
}elseif($_REQUEST['t']==4){
    $t      = 'Ejecución Presupuestal Consolidada Gerencial de Ingresos Por Fuente';
    $rowri  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 6 ORDER BY codi_presupuesto ASC");
    $rowrf  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 6 ORDER BY codi_presupuesto DESC");
    $action = 4 ;
}elseif($_REQUEST['t']==5){
    $t      = 'Ejecución Presupuestal Consolidada Gerencial de Gastos Por IE';
    $rowri  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 7 ORDER BY codi_presupuesto ASC");
    $rowrf  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 7 ORDER BY codi_presupuesto DESC");
    $action = 5 ;
}elseif($_REQUEST['t']==6){
    $t      = 'Ejecución Presupuestal Consolidada Gerencial de Ingresos Por IE';
    $rowri  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 6 ORDER BY codi_presupuesto ASC");
    $rowrf  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 6 ORDER BY codi_presupuesto DESC");
    $action = 6 ;
}elseif($_REQUEST['t']==7){
    $t      = 'Ejecución Presupuestal Consolidada Gerencial de Gastos Por Ciudad';
    $rowri  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 7 ORDER BY codi_presupuesto ASC");
    $rowrf  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 7 ORDER BY codi_presupuesto DESC");
    $action = 7 ;
}elseif($_REQUEST['t']==8){
    $t      = 'Ejecución Presupuestal Consolidada Gerencial de Ingresos Por Ciudad';
    $rowri  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 6 ORDER BY codi_presupuesto ASC");
    $rowrf  = $con->Listar("SELECT codi_presupuesto, nombre FROM gf_rubro_pptal WHERE parametrizacionanno = $anno AND tipoclase = 6 ORDER BY codi_presupuesto DESC");
    $action = 8 ;
}

 ?>
<title><?php echo $t?></title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #sltfechaI-error, #sltfechaF-error, #sltcni-error, #sltcnf-error  {
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;
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
    },
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
</head>
<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px"> 
            <h2 align="center" class="tituloform"><?php echo $t?></h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data"  action="javaScript:reporte()">  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group">
                        <input type="hidden" name="sltAnnio" id="sltAnnio" value="<?php echo $anno;?>">
                        <input type="hidden" name="action" id="action" value="<?php echo $action;?>">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltmesf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Periodo Final:</label>
                            <select required name="sltmesf" id="sltmesf" style=" height: auto" class="select2_single form-control" title="Mes Final" >
                                   <option value="">Mes Final</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltcodi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Inicial:</label>
                            <select required="required" name="sltcodi" id="sltcodi" class="select2_single form-control" title="Seleccione Cuenta Inicial" >
                                <option value=""> Código Inicial</option> 
                                <?php for ($i = 0; $i < count($rowri); $i++) {
                                     echo '<option value="'.$rowri[$i][0].'">'.$rowri[$i][0].' - '.$rowri[$i][1].'</option> ';
                                 }?>
                            </select>
                        </div>
                       <div class="form-group" style="margin-top: -0px">
                            <label for="sltcodf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Código Final:</label>
                            <select required="required" name="sltcodf" id="sltcodf" class="select2_single form-control" title="Seleccione Cuenta Final">
                                <option value=""> Código Final</option>         
                                <?php for ($i = 0; $i < count($rowrf); $i++) {
                                     echo '<option value="'.$rowrf[$i][0].'">'.$rowrf[$i][0].' - '.$rowrf[$i][1].'</option> ';
                                 }?>
                            </select>
                        </div>
                        
                        <div class="col-sm-10" style="margin-top:0px;margin-left:700px" >
                            <button type="submit" id="pdf" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                            <input type="hidden" name="tipo" id="tipo" value="0">
                            <button style="margin-left:10px;" type="submit" id="excel" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </form>
            </div>     
        </div>
    </div>
</div>
<script src="js/select/select2.full.js"></script>
<script>
$(document).ready(function() {
  $(".select2_single").select2({
    allowClear: true
  });
  let action = $("#action").val();
  if(action==1 || action==2){
      $("#pdf").css("display", "inline-block");
  } else {
      $("#pdf").css("display", "none");
  }
});
$("#pdf").click(function(){
    $("#tipo").val(1);
})
$("#excel").click(function(){
    $("#tipo").val(2);
});
</script>
<script>    
    $(document).ready(function() {
       
       var form_data={action: 2, annio :$("#sltAnnio").val()};
       var optionMF ="<option value=''>Periodo Final</option>";
       $.ajax({
          type:'POST', 
          url:'jsonPptal/consultasInformesPptal.php',
          data: form_data,
          success: function(response){
              optionMF =optionMF+response;
              $("#sltmesf").html(optionMF).focus();              
          }
       });
       
    });
</script>
<?php require_once 'footer.php'?>  
<script>
function reporte(){
    let action = $("#action").val();
    var formData = new FormData($("#form")[0]);  
    jsShowWindowLoad('Generando Informe...');
    var form_data = { action:1 };
    $.ajax({
        type: 'POST',
        url: "jsonPptal/gf_consolidadoJson.php?action="+action,
        data:formData,
        contentType: false,
        processData: false,
        success: function(response)
        {   jsRemoveWindowLoad();
            console.log(response);
            var m2 = $("#sltmesf").val();
            var c1 = $("#sltcodi").val();
            var c2 = $("#sltcodf").val();
            var tipo = $("#tipo").val();
            if(action ==1 || action == 2){
                window.open('informes_consolidado/INFORME_EJECUCION.php?c='+action+'&t='+tipo+'&sltmesf='+m2+'&sltcodi='+c1+'&sltcodf='+c2);
            } else {
                window.open('informes_consolidado/INF_GERENCIALES_CONSOLIDADO.php?c='+action+'&t='+tipo+'&sltmesf='+m2+'&sltcodi='+c1+'&sltcodf='+c2);
            }
        }
    });
}
</script>
</body> 
</html>