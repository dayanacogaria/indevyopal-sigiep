<?php
@session_start();
require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
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


   <title>Cesantías Retroactivas</title>
    <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Cesantías Retroactivas</h2>
                    
                    <div class="client-form contenedorForma" style="margin-top: -6px;font-size: 13px">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonNomina/gn_Liquidacion_Cesantias_Retroactivas.php?t=1">                              
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <div class="form-group form-inline" style="margin-top:-25px; height: 100px;">
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
                                <label for="sltEmpleado" class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Empleado:
                                </label>
                                <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 210px;height: 30px" class="form-control col-sm-2">
                                     <option value="2">VARIOS</option>
                                        <?php 
                                            while($rowE = mysqli_fetch_row($empleado))
                                            {
                                                echo "<option value=".$rowE[0].">".$rowE[3]."</option>";
                                            }
                                        ?>                                                          
                                </select>

                          <!--------------------------------------------------------------------- -->
                        <?php
                            $per = "SELECT  p.id_unico, CONCAT(p.codigointerno,' - ',tpn.nombre) FROM gn_periodo p LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico  WHERE tpn.id_unico = 16 AND p.liquidado !=1 AND p.parametrizacionanno = '$vig'";

                            $periodo = $mysqli->query($per);
                        ?>
                        <label for="sltPeriodo"  class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Periodo:</label>
                        <select required="required" name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="width: 210px;height: 30px" class="form-control col-sm-2">
                            <option value="">Periodo</option>
                            <?php 
                                while($rowE = mysqli_fetch_row($periodo))
                                {
                                    echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                }
                            ?>       
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
        var fechain= document.getElementById('sltFechaI').value;
        var fechafi= document.getElementById('sltFechaF').value;
          var fi = document.getElementById("sltFechaF");
        fi.disabled=false;
      
       
            $( "#sltFechaF" ).datepicker( "destroy" );
            $( "#sltFechaF" ).datepicker({ changeMonth: true, minDate: fechain});
    
                   
}


function fechaDisfrute(){
        var fechain= document.getElementById('sltFechaID').value;
        var fechafi= document.getElementById('sltFechaFD').value;
          var fi = document.getElementById("sltFechaFD");
        fi.disabled=false;
      
       
            $( "#sltFechaFD" ).datepicker( "destroy" );
            $( "#sltFechaFD" ).datepicker({ changeMonth: true, minDate: fechain});
        

           
           
}
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
