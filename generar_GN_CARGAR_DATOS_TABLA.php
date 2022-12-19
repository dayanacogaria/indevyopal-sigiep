<?php
###################################################################################################
#
#08/08/2017 creado por Nestor B 
#
####################################################################################################

require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
#session_start();
$con      = new ConexionPDO();
$vig = $_SESSION['anno'];
@$id = $_GET['idE'];

$compania =$_SESSION['compania'];
$emp = "SELECT e.id_unico, e.tercero, CONCAT( t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ) , t.tipoidentificacion, ti.id_unico, CONCAT(ti.nombre,' ',t.numeroidentificacion)
FROM gn_empleado e
LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
WHERE md5(e.id_unico) = '$id'";
$bus = $mysqli->query($emp);
$busq = mysqli_fetch_row($bus);
$idT = $busq[0];
$datosTercero= $busq[2].' ('.$busq[5].')';
$a = "none";
if(empty($idT))
{

    $tercero = "Empleado";    
}
else
{
    $tercero = $datosTercero;
    $a="inline-block";
}


$rowe = $con->Listar("SELECT DISTINCT tr.id_unico, IF(CONCAT_WS(' ',
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos) 
        IS NULL OR CONCAT_WS(' ',
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos) = '',
        (tr.razonsocial),
        CONCAT_WS(' ',
        tr.nombreuno,
        tr.nombredos,
        tr.apellidouno,
        tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion 
        FROM gn_empleado e 
    LEFT JOIN gn_novedad n ON n.empleado = e.id_unico 
    LEFT JOIN gf_tercero tr ON tr.id_unico = e.tercero 
    LEFT JOIN gn_periodo p ON n.periodo = p.id_unico 
    WHERE p.parametrizacionanno = $vig
    ORDER BY e.id_unico ASC");
?>

<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
    label #sltPeriodo-error {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;
        font-size: 10px
    }

    body{
        font-size: 11px;
    }
    
   /* Estilos de tabla*/
   table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
   table.dataTable tbody td,table.dataTable tbody td{padding:1px}
   .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
       font-family: Arial;}
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
<script src="js/jquery-ui.js"></script>

   <title>Cargar Datos Personal Costos-Año</title>
    <link href="css/select/select2.min.css" rel="stylesheet">
       
    </head>
    <body>
        <div class="container-fluid text-center">
        <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-8 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Cargar Datos Personal Costos-Año</h2>
               
                <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">        
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes_nomina/generar_INFORME_PERSONAL_COSTOS.php" target="_blank">
                        <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                       
        <!--<div class="container-fluid text-center">
              <div class="row content">
                  <?php #require_once 'menu.php'; ?>
                  <div class="col-sm-8 text-left" style="margin-top: 0px">
                      <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Liquidación Nómina</h2>
                      <a href="<?php #echo 'listar_GN_VACACIONES.php?id='.$_GET['idE'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php #echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                      <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php #echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php #echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                      <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/liquidarNominaJson.php">
                              <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p> -->                                         
<!--------------------------------------------------------------------------------------------------------------------- -->
                                <?php
                                    $concepto = "SELECT codigo, CONCAT(codigo,' - ',descripcion) FROM gn_concepto WHERE unidadmedida = 1";
                                    $Con = $mysqli->query($concepto);
                                ?>
                          <?php               
                                $annio = "SELECT  id_unico, anno FROM gf_parametrizacion_anno WHERE compania = $compania ORDER BY anno DESC";
                                $rsannio = $mysqli->query($annio);
                                
                            
                                ?>
                                 
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltAnnio" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Año:</label>
                            <select onclick="activa()" required name="sltAnnio" id="sltAnnio" style="height: auto" class="select2_single form-control" title="Seleccione el año" >
                                <option value="">Año</option>
                                <?php while($annio = mysqli_fetch_row($rsannio)){

                               echo "<option value=".$annio[0].">".$annio[1]."</option>";

                                }
                                 ?>

                            
                            </select>

                        </div> 
                      <!--  <div class="form-group" style="margin-left: 400px;">
                            <input id="empleados"  name="empleados" type="checkbox"   /><strong style=" font-size: 12px; ">&nbsp;&nbsp;Detallado Empleado</strong>
                            
                        </div>-->
                         <div id="fechas" style="display:none">
                                <div class="form-group">
                                <label for="Empleado" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Empleado:</label>
                            <select   name="Empleado" id="Empleado" class="select2_single form-control" title="Seleccione Empleado">
                                <option value="">Empleado</option>
                                <?php for ($i = 0; $i < count($rowe); $i++) {
                                    echo '<option value="'.$rowe[$i][0].'">'.$rowe[$i][1].' - '.$rowe[$i][2].'</option>';
                                } ?>                                    
                            </select>
                                </div> 
                        </div>
                        
                                
                                <div class="form-group" style="margin-top: -5px">   
                                    <label for="no" class="col-sm-5 control-label"></label>
                                    <button  class="btn sombra btn-primary" title="Generar reporte PDF" style="margin-top: 15px;"><i>Cargar Datos Tabla</li></button>
                                </div>
                            


                                
                        
                  </div>
                

            </div>
           
      </div>                                    
    </div>
   <div>

<?php require_once './footer.php'; ?>

      


  <!--Script que dan estilo al formulario-->

  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
<!--Scrip que envia los datos para la eliminación-->
<script src="js/select/select2.full.js"></script>

<script type="text/javascript"> 
         $("#sltAnnio").select2();
            $("#Empleado").select2();
</script>
<script type="text/javascript"> 
 $(document).ready(function(){

$('#empleados').change(function(){


if (document.getElementById('empleados').checked)
{
  $("#fechas").css("display", "block");
}else{
  $("#fechas").css("display", "none");
}


  });


 });

</script>
</body>
</html>