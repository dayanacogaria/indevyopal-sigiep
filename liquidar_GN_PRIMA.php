<?php
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
    label #id_per-error, #id_emp-error {
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
   <title>Liquidación Prima</title>
    <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Liquidación Prima</h2>
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="JavaScript:guardar()">
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
                                    $idTer = "";
                                    $empleado = $mysqli->query($emp);
                                ?>
                                <label for="id_emp" class="col-sm-2 control-label"><strong class="obligado">*</strong>Empleado:</label>
                                <select required="required" name="id_emp" id="id_emp" title="Seleccione Empleado" style="width: 140px;height: 30px" class="form-control col-sm-1">
                                      <option value="2">VARIOS</option>
                                        <?php while($rowE = mysqli_fetch_row($empleado)) {
                                            echo "<option value=".$rowE[0].">".$rowE[3]."</option>";
                                        } ?>                                                          
                                </select>
                                <?php
                                    $per = "SELECT  p.id_unico, CONCAT(p.codigointerno,' - ',tpn.nombre) FROM gn_periodo p LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico "
                                            . "WHERE tpn.id_unico = 2 AND p.liquidado !=1 AND p.parametrizacionanno = '$vig'";
                                    $periodo = $mysqli->query($per);
                                ?>
                                <label for="id_per" class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Periodo:
                                </label>
                                <select required="required" name="id_per" id="id_per" title="Seleccione Periodo" style="width: 140px;height: 30px" class="form-control col-sm-1">
                                        <?php while($rowE = mysqli_fetch_row($periodo)){
                                            echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                        }?>       
                                </select>

                                <label for="No" class="col-sm-2 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top: -3px; width:100px; margin-bottom: -10px;margin-left: 10px ;">Liquidar</button>    
                            </div>
                        </form>    
                          
                    </div>
                </div>
            </div>
           
      </div>                                    
    </div>
   <div>
<?php require_once './footer.php'; ?>
  <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <label id="mensaje"></label>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" id="Cancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script type="text/javascript" src="js/select2.js"></script>
  <script>
      function guardar(){
        var formData = new FormData($("#form")[0]);  
        jsShowWindowLoad('Guardando Información...');
        var form_data = { action:1 };
        $.ajax({
            type: 'POST',
            url: "json/liquidarPrimaJson.php",
            data:formData,
            contentType: false,
            processData: false,
            success: function(response)
            { 
                jsRemoveWindowLoad();
                console.log(response+'G');
                if(response ==0){
                    $("#mensaje").html('No Se Ha Podido Guardar Información');
                    $("#modalMensajes").modal("show");
                    $("#Aceptar").click(function(){
                        $("#modalMensajes").modal("hide");
                    }) 
                } else {
                    $("#mensaje").html('Información Guardada Correctamente');
                    $("#modalMensajes").modal("show");
                    $("#Aceptar").click(function(){
                        window.open('informes_nomina/generar_INF_SABANA_PRIMA_S.php?t=1&sltPeriodo='+$("#id_per").val());
                    })                       
                }
            }
        });
    }
    </script>
    <script type="text/javascript"> 
         $("#id_per").select2();
         $("#id_emp").select2();
    </script>
</body>
</html>
