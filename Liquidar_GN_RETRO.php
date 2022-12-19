<?php

require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
#session_start();
$vig = $_SESSION['anno'];
$compania = $_SESSION['compania'];
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script src="js/jquery-ui.js"></script>

<style>
    label #sltEmpleado-error, #sltPeriodo-error, #fechaR-error {
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
   <title>Liquidación Retroactivo</title>
    <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Liquidación Retroactivo</h2>                    
                    <div class="client-form contenedorForma" style="margin-top: -6px;font-size: 13px">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonNomina/gn_LiquidacionRetroactivo.php?t=1">                              
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <div class="form-group form-inline" style="margin-top:-25px; height: 100px;">
                                <?php 
                                    $emp = "SELECT e.id_unico,e.tercero,t.id_unico,CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno,t.apellidodos ) 
                                    FROM gn_empleado e
                                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico 
                                    LEFT JOIN gn_empleado_tipo et ON et.empleado = e.id_unico 
                                    LEFT JOIN gn_tipo_empleado te ON et.tipo = te.id_unico 
                                    where t.compania = $compania AND te.porcentaje_retroactivo IS NOT NULL";
                                    $empleado = $mysqli->query($emp);
                                ?>
                                <label for="sltEmpleado" class="col-sm-2 control-label"><strong class="obligado">*</strong>Empleado:</label>
                                <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 210px;height: 30px" class="form-control col-sm-2">
                                    <option value="2">VARIOS</option>
                                        <?php while($rowE = mysqli_fetch_row($empleado)) { echo "<option value=".$rowE[0].">".$rowE[3]."</option>"; } ?>
                                </select>                                  
                                <?php $per = "SELECT  p.id_unico, CONCAT(p.codigointerno,' - ',tpn.nombre) FROM gn_periodo p LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico  WHERE tpn.id_unico =12 AND p.liquidado !=1 AND p.parametrizacionanno = '$vig'";
                                    $periodo = $mysqli->query($per); ?>
                                <label for="sltPeriodo"  class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Periodo:</label>
                                <select required="required" name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="width: 210px;height: 30px" class="form-control col-sm-2">
                                    <option value="">Periodo</option>
                                    <?php while($rowE = mysqli_fetch_row($periodo)) { echo "<option value=".$rowE[0].">".$rowE[1]."</option>"; } ?>       
                                </select>
                                <button type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top: 0px; width:100px; margin-bottom: -15px;margin-left: 10px ;">Liquidar</button>  

                            </div>
                        </form> 
                    </div>
                </div>
            </div>

           
      </div>                                    
    </div>
   <div>
<?php require_once './footer.php'; ?>

  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>


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
</script>
<script type="text/javascript" src="js/select2.js"></script>
<script type="text/javascript"> 
         $("#sltEmpleado").select2();
        </script>
<script type="text/javascript"> 
         $("#sltPeriodo").select2();
</script>
        
</body>
</html>
