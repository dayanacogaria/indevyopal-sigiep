<?php 
require_once 'Conexion/conexion.php';  
require_once 'head_listar.php';
$url = $_SESSION['url'];
if(empty($_GET['id'])){?>
 <script>
     document.location="<?php echo $url?>";
 </script>
    
<?php } else {
  $id= $_GET['id'];  
  $busqueda = "SELECT RP.id_unico,
       RP.nombre,
       RP.codi_presupuesto, 
       CPT.nombre  
   FROM gf_rubro_pptal RP
   LEFT JOIN gf_tipo_clase_pptal CPT ON RP.tipoclase = CPT.id_unico
   LEFT JOIN gf_destino DT ON RP.destino = DT.id_unico
   LEFT JOIN gf_tipo_vigencia TV ON RP.tipovigencia = TV.id_unico
   LEFT JOIN gf_sector SC ON RP.sector = SC.id_unico
   WHERE md5(RP.id_unico) = '$id'";
  $busqueda = $mysqli->query($busqueda);
  $rubro = mysqli_fetch_row($busqueda);
  $idR=$rubro[0];
  $nombreCod = $rubro[2].' - '.$rubro[1];
  $codigo = $rubro[2];
  $tipo = mb_strtolower($rubro[3]);
}

?> 
<title>Movimiento Rubro Pptal</title>
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
label#Tercero-error, #TipoResponsable-error, #tipoRel-error{
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
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:10px">Movimiento Rubro Pptal</h2>
              <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; margin-top:-5px;vertical-align:middle;text-decoration:none" title="Volver"></a>

              <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-20px;  background-color: #0e315a; color: white; border-radius: 5px">Rubro: <?php echo ucwords(mb_strtolower($nombreCod));?></h5>

                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                        <?php $fuente = "SELECT id_unico, nombre FROM gf_fuente ORDER BY id_unico";
                        $fuente = $mysqli->query($fuente);?>
                        <input type="hidden" id="tipo" name="tipo" value="<?php echo $tipo;?>">
                        <input type="hidden" id="codigo" name="codigo" value="<?php echo $codigo;?>">
                        <div class="form-group" style="margin-top: 15px">
                            <label for="sltmesI" class="col-sm-5 control-label">Mes Inicial:</label>
                            <select name="sltmesI" id="sltmesI" class="select2_single form-control" title="Mes Inicial" >
                                <option value>Mes Inicial</option>
                                <option value = "01">Enero</option>
                                <option value = "02">Febrero</option>
                                <option value = "03">Marzo</option>
                                <option value = "04">Abril</option>
                                <option value = "05">Mayo</option>
                                <option value = "06">Junio</option>
                                <option value = "07">Julio</option>
                                <option value = "08">Agosto</option>
                                <option value = "09">Septiembre</option>
                                <option value = "10">Octubre</option>
                                <option value = "11">Noviembre</option>
                                <option value = "12">Diciembre</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltmesF" class="col-sm-5 control-label">Mes Final:</label>
                            <select name="sltmesF" id="sltmesF" class="select2_single form-control" title="Mes Final" >
                                <option value>Mes Final</option>
                                <option value = "12">Diciembre</option>
                                <option value = "11">Noviembre</option>
                                <option value = "10">Octubre</option>
                                <option value = "09">Septiembre</option>
                                <option value = "08">Agosto</option>
                                <option value = "07">Julio</option>
                                <option value = "06">Junio</option>
                                <option value = "05">Mayo</option>
                                <option value = "04">Abril</option>
                                <option value = "03">Marzo</option>
                                <option value = "02">Febrero</option>
                                <option value = "01">Enero</option> 
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 15px">
                        <div class="col-sm-10" style="margin-top:0px;margin-left:610px" >
                            <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                        
                            <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
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
        allowClear: true,
      });
    });
  </script>


     <?php require_once 'footer.php'; ?>
<script type="text/javascript" src="js/menu.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<script>
    function reportePdf(){
        var tipo = document.getElementById('tipo').value;
        switch(tipo){
            case ('ingresos'):
                $('form').attr('action', 'informes/INFORMES_RUBRO_PPTAL/Informe_Movimiento_Ingresos_Pdf.php');
            break;
            case ('gastos'):
                $('form').attr('action', 'informes/INFORMES_RUBRO_PPTAL/Informe_Movimiento_Gastos_Pdf.php');
            break;
            case ('cuentas por pagar vigencia anterior'):
                $('form').attr('action', 'informes/INFORMES_RUBRO_PPTAL/Informe_Movimiento_Gastos_Pdf.php');
            break;
        }
    }
    function reporteExcel(){
        var tipo = document.getElementById('tipo').value;
        switch(tipo){
            case ('ingresos'):
                $('form').attr('action', 'informes/INFORMES_RUBRO_PPTAL/Informe_Movimiento_Ingresos_Excel.php');
            break;
            case ('gastos'):
                $('form').attr('action', 'informes/INFORMES_RUBRO_PPTAL/Informe_Movimiento_Gastos_Excel.php');
            break;
            case ('cuentas por pagar vigencia anterior'):
                $('form').attr('action', 'informes/INFORMES_RUBRO_PPTAL/Informe_Movimiento_Gastos_Excel.php');
            break;
        }
        
    }
</script>

</body>
</html>

