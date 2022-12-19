<?php
require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
#session_start();
$vig = $_SESSION['anno'];
@$id = $_GET['idE'];
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



?>

<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<style>
    label #sltEmpleado-error, #sltPeriodo-error {
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

   <title>Generación Volante de Pago</title>
   
       
    </head>
    <body>
        <div class="container-fluid text-center">
        <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-8 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Generación de Volante de Pago</h2>
               
                <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5>
                <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px">        
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>
                        <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline" style="margin-left: 40px">
                        <div class="form-group form-inline " style="margin-top: -25px;"> 
                            
                          <?php 
                       if(empty($idT))
                        {
                         $emp = "SELECT                         
                                                        e.id_unico,
                                                        e.tercero,
                                                        tr.id_unico,
                                                        IF(CONCAT_WS(' ',
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
                                                         tr.apellidodos))
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico";
                            $idTer = "";
                        }
                        else
                        {
                        $emp = "SELECT                      
                                                        e.id_unico,
                                                        e.tercero,
                                                        tr.id_unico,
                                                        IF(CONCAT_WS(' ',
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
                                                             tr.apellidodos))
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero tr ON e.tercero = tr.id_unico WHERE e.id_unico != $idT";
                        $idTer = $idT;
                        }
                        $empleado = $mysqli->query($emp);
                        ?>
                          <label for="sltEmpleado" class="control-label col-sm-1" style="margin-top: 15px">
                                <strong style="color:#03C1FB;">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 200px;height: 30px; margin-top: 15px; margin-left: 35px;" class="select2_single form-control col-sm-1" required>
                                <option value="<?php echo $idTer?>"><?php echo $tercero?></option>
                                <?php 
                                    while($rowE = mysqli_fetch_row($empleado))
                                    {
                                        echo "<option value=".($rowE[0]).">".$rowE[3]."</option>";
                                    }
                                ?>                                                          
                            </select>
                          <!--------------------------------------------------------------------- -->
                            <?php
                                $per = "SELECT  id_unico, codigointerno FROM gn_periodo WHERE tipoprocesonomina = 1 AND parametrizacionanno ='$vig'";

                                $periodo = $mysqli->query($per);
                            ?>

                            
                          <label for="sltPeriodo" class="control-label col-sm-1" style="margin-top: 15px; margin-left: 60px;">
                                <strong style="color:#03C1FB;">*</strong>Periodo:
                            </label> 
                          <select  name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="width: 140px;height: 30px; margin-top: 15px; margin-left: 35px;" class="select2_single form-control col-sm-1" required>
                                
                                <?php 
                                    while($rowE = mysqli_fetch_row($periodo))
                                    {
                                        echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                    }
                                ?>       
                            </select>
                                                        
                            <button style="margin-top:10px;" onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>
                            <button style="margin-left:10px;margin-top:10px" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                        </div>
                          
                        </div>
                  </div>
      </div>                                    
    </div>
   <div>
</div>
<?php require_once './footer.php'; ?>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/select2.js"></script>
<script type="text/javascript"> 
     $("#sltEmpleado").select2();
     $("#sltPeriodo").select2();
</script>
<script>
function reportePdf(){
    $('form').attr('action', 'informes/generar_INF_VOLANTE_PAGO.php');
}
function reporteExcel(){
    $('form').attr('action', 'informes/generar_INF_VOLANTE_PAGOXLS.php');
}
</script>

</body>
</html>