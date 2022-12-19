<?php 
###############MODIFICACIONES###############################
#02/03/2017 | ERICA G. | ARCHIVO CREADO
############################################################
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
##CONSULTAS COMBOS##
$disI ="SELECT
  cp.id_unico,
  cp.numero numero,
  tc.codigo,
  cp.fecha,
  IF(CONCAT_WS(' ',
      ter.nombreuno,
      ter.nombredos,
      ter.apellidouno,
      ter.apellidodos) 
      IS NULL OR CONCAT_WS(' ',
      ter.nombreuno,
      ter.nombredos,
      ter.apellidouno,
      ter.apellidodos) = '',
    (ter.razonsocial),
    CONCAT_WS(' ',
      ter.nombreuno,
      ter.nombredos,
      ter.apellidouno,
      ter.apellidodos)) AS NOMBRE,
  (SELECT SUM(dc.valor)
  FROM
    gf_detalle_comprobante_pptal dc
  WHERE
    cp.id_unico = dc.comprobantepptal) AS valor
FROM
  gf_comprobante_pptal cp
LEFT JOIN
  gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
LEFT JOIN
  gf_tercero ter ON cp.tercero = ter.id_unico
WHERE
  tc.clasepptal = 14 AND tc.tipooperacion = 1 
  AND cp.parametrizacionanno = $anno 
ORDER BY
  cp.numero ASC";
$disI = $mysqli->query($disI);
##FINAL##
$disF = "SELECT
  cp.id_unico,
  cp.numero numero,
  tc.codigo,
  cp.fecha,
  IF(CONCAT_WS(' ',
      ter.nombreuno,
      ter.nombredos,
      ter.apellidouno,
      ter.apellidodos) 
      IS NULL OR CONCAT_WS(' ',
      ter.nombreuno,
      ter.nombredos,
      ter.apellidouno,
      ter.apellidodos) = '',
    (ter.razonsocial),
    CONCAT_WS(' ',
      ter.nombreuno,
      ter.nombredos,
      ter.apellidouno,
      ter.apellidodos)) AS NOMBRE,
  (SELECT SUM(dc.valor)
  FROM
    gf_detalle_comprobante_pptal dc
  WHERE
    cp.id_unico = dc.comprobantepptal) AS valor
FROM
  gf_comprobante_pptal cp
LEFT JOIN
  gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
LEFT JOIN
  gf_tercero ter ON cp.tercero = ter.id_unico
WHERE
  tc.clasepptal = 14 AND tc.tipooperacion = 1
  AND cp.parametrizacionanno = $anno 
ORDER BY
  cp.numero DESC";
$disF = $mysqli->query($disF);
#TIPO COMPROBANTE#
$tipo = "SELECT DISTINCT id_unico, codigo, nombre "
        . "FROM gf_tipo_comprobante_pptal "
        . "WHERE vigencia_actual = 1 "
        . "AND clasepptal = 14 AND tipooperacion = 1 "
        . "AND compania = $compania "
        . "ORDER BY codigo ASC";
$tipo = $mysqli->query($tipo);

?>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>


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
    },
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>

   <style>
    .form-control {font-size: 12px;}
    
</style>

<title>Seguimiento a Disponibilidad</title>
</head>
<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px"> 
            <h2 align="center" class="tituloform">Seguimiento a Disponibilidad</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action=""  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="tipo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Comprobante:</label>
                            <select  name="tipo" id="tipo" class="select2_single form-control" title="Seleccione Tipo Comprobante">
                                <option value>Tipo Comprobante</option>
                                <?php while ($rowt= mysqli_fetch_row($tipo)){ ?>
                                <option value="<?php echo $rowt[0];?>"><?php echo mb_strtoupper($rowt[1]).' '.ucwords(mb_strtolower($rowt[2]));?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="disI" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Disponibilidad Inicial:</label>
                            <select  name="disI" id="disI" class="select2_single form-control" title="Seleccione Disponibilidad Inicial">
                                <option value>Disponibilidad Inicial</option>
                                <?php while ($row= mysqli_fetch_row($disI)){ 
                                    $source = $row[3];
                                    $date = new DateTime($source);
                                    $fecha= $date->format('d/m/Y') ?>
                                <option value="<?php echo $row[1];?>"><?php echo $row[1].' '.mb_strtoupper($row[2]).' '.ucwords(mb_strtolower($row[4].' '.$fecha.' $'.number_format($row[5])));?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 0px">
                            <label for="disF" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Disponibilidad Final:</label>
                            <select  name="disF" id="disF" class="select2_single form-control" title="Seleccione Disponibilidad final">
                                <option value>Disponibilidad Final</option>
                                <?php while ($row1 = mysqli_fetch_row($disF)) { 
                                    $source1 = $row1[3];
                                    $date1 = new DateTime($source1);
                                    $fecha1= $date1->format('d/m/Y') ?>
                                <option value="<?php echo $row1[1];?>"><?php echo $row1[1].' '.mb_strtoupper($row1[2]).' '.ucwords(mb_strtolower($row1[4].' '.$fecha1.' $'.number_format($row1[5])));?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        <div class="form-group text-center" style="margin-top:20px;">
                            <div class="col-sm-1" style="margin-top:0px;margin-left:620px">
                                <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                            </div>
                            <div class="col-sm-1" style="margin-top:-34px;margin-left:670px">
                                <button onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
                <!--SCRIPT PARA CAMBIAR CONSULTAS -->
                <script>
                    $(document).ready(function()
                        {
                          $("#tipo").change(function()
                          {
                            var tipo = $('#tipo').val();
                           // var opcion = '<option val="">Disponibilidad Inicial</option>';
                            if(tipo==''){
                                
                            } else {
                                var form_data = { case:19,tipo: tipo};

                                $.ajax({
                                  type: "POST",
                                  url: "consultasBasicas/busquedas.php",
                                  data: form_data,
                                  success: function(response)
                                  {
                                      
                                  if(response != 0)
                                    {
                                      
                                      $('#disI').html(response).fadeIn();
                                      $('#disI').css('display', 'none');
                                      $('#disI').focus();
                                    }
                                  }
                              });
                              var form_data = { case:20,tipo: tipo};

                                $.ajax({
                                  type: "POST",
                                  url: "consultasBasicas/busquedas.php",
                                  data: form_data,
                                  success: function(response)
                                  {
                                  if(response != 0)
                                    {
                                     
                                      $('#disF').html(response).fadeIn();
                                      $('#disF').css('display', 'none');
                                      $('#disF').focus();
                                    }
                                  }
                              });
                            }
                          
                          });
                        });
                </script>
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
    });
</script>
<script>
function reportePdf(){
    $('form').attr('action', 'informes/informe_seguimiento_pdf.php');
    }
</script>
<script>
function reporteExcel(){
    
    $('form').attr('action', 'informes/informe_seguimiento_excel.php');
    
}

</script>
</body>
</html>
<?php require_once 'footer.php'?>  