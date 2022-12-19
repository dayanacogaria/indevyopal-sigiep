<?php

require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
@session_start();

$vig = $_SESSION['anno'];
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script src="js/jquery-ui.js"></script>

<style>
    label #sltEmpleado-error, #sltPeriodo-error {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;
        font-size: 10px
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
<script src="js/jquery-ui.js"></script>
   <title>Liquidación Prima de Navidad</title>
    <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Liquidación de Prima de Navidad</h2>
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonNomina/gn_primaNavidad.php?t=1">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <div class="form-group form-inline" style="margin-top:-25px">
                                <?php                                     
                                $emp = "SELECT                         
                                                        e.id_unico,
                                                        e.tercero,
                                                        t.id_unico,
                                                        IF(CONCAT_WS(' ',
                                                             t.nombreuno,
                                                             t.nombredos,
                                                             t.apellidouno,
                                                             t.apellidodos) 
                                                             IS NULL OR CONCAT_WS(' ',
                                                             t.nombreuno,
                                                             t.nombredos,
                                                             t.apellidouno,
                                                             t.apellidodos) = '',
                                                             (t.razonsocial),
                                                             CONCAT_WS(' ',
                                                             t.nombreuno,
                                                             t.nombredos,
                                                             t.apellidouno,
                                                             t.apellidodos)) AS NOMBRE
                                                    FROM gn_empleado e
                                                    LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                                                    LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                                    LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                                    WHERE et.id_unico IS NOT NULL";
                                    $empleado = $mysqli->query($emp);
                                ?>
                                <label for="sltEmpleado" class="col-sm-2 control-label"><strong class="obligado">*</strong>Empleado:</label>
                                <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 140px;height: 30px" class="form-control col-sm-1">
                                    <option value="">Empleado</option>
                                    <option value="2">VARIOS</option>
                                    <?php while($rowE = mysqli_fetch_row($empleado)) {
                                            echo "<option value=".$rowE[0].">".$rowE[3]."</option>";
                                        }
                                    ?>                                                          
                                </select>
                                <!--------------------------------------------------------------------- -->
                                <?php
                                    $per = "SELECT  p.id_unico, CONCAT(p.codigointerno,' - ',tpn.nombre) "
                                        . "FROM gn_periodo p LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico "
                                        . "WHERE p.id_unico!=1 AND p.liquidado !=1 AND p.tipoprocesonomina = 8 AND p.parametrizacionanno = '$vig'";                                    
                                    $periodo = $mysqli->query($per);
                                ?>

                                <label for="sltPeriodo" class="col-sm-2 control-label"><strong class="obligado">*</strong>Periodo:</label>
                                <select required="required" name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="width: 140px;height: 30px" class="form-control col-sm-1">
                                    <option value="">Periodo</option>
                                    <?php  while($rowE = mysqli_fetch_row($periodo)){
                                            echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                        }
                                    ?>       
                                </select>
                                <label for="No" class="col-sm-2 control-label"></label>
                                <button type="submit" id="liquidar"  title ="Liquidar"  class="btn btn-primary shadow" ><li   class="glyphicon glyphicon-usd" ></li></button>  
                            </div>
                        </form>  
                    </div>
                </div>
            </div>
        </div>
        <script src="js/md5.js"></script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
            $("#sltEmpleado").select2();
            $("#sltPeriodo").select2();
        </script>
    </body>
</html>