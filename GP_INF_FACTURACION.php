<?php 
##################################################################################################
#********************************** Modificaciones ¨*********************************************#
##################################################################################################
#03/10/2018 |Creado
##################################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 
$con = new ConexionPDO();
$anno = $_SESSION['anno'];?>
<title>Informe Facturación</title> 
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #p-error, #s2-error, #s1-error {
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
    },
    rules: {
        sltmes: {
          required: true
        },
        sltcni: {
          required: true
        },
        sltAnnio: {
          required: true
        }
     }
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Informe Facturación</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes_servicios/INF_FACTURAS.php" target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="p" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Periodo:</label>
                            <select name="p" id="p" class="form-control select2" title="Seleccione Periodo" style="height: auto " required>
                                <?php 
                                    echo '<option value="">Periodo</option>';
                                    $tr = $con->Listar("SELECT p.* FROM gp_periodo p 
                                        LEFT JOIN gp_ciclo c ON p.ciclo = c.id_unico 
                                        WHERE c.estado_facturacion NOT IN(7,9) 
                                        AND p.anno = $anno ORDER BY p.fecha_inicial DESC");
                                    for ($i = 0; $i < count($tr); $i++) {
                                       echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).' - '.$tr[$i]['descripcion'].'</option>'; 
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="s1" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Sector Inicial:</label>
                            <select name="s1" id="s1" class="form-control select2" title="Seleccione Sector Inicial" style="height: auto " required>
                                <?php 
                                    echo '<option value="">Sector Inicial</option>';
                                    $tr = $con->Listar("SELECT * FROM gp_sector ORDER BY id_unico ASC");
                                    for ($i = 0; $i < count($tr); $i++) {
                                       echo '<option value="'.$tr[$i][0].'">'.$tr[$i][2].' - '.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="s2" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Sector Final:</label>
                            <select name="s2" id="s2" class="form-control select2" title="Seleccione Sector Final" style="height: auto " required>
                                <?php 
                                    echo '<option value="">Sector Final</option>';
                                    $tr = $con->Listar("SELECT * FROM gp_sector ORDER BY id_unico DESC");
                                    for ($i = 0; $i < count($tr); $i++) {
                                       echo '<option value="'.$tr[$i][0].'">'.$tr[$i][2].' - '.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="uso" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado"></strong>Uso:</label>
                            <select name="uso" id="uso" class="form-control select2" title="Seleccione Uso" style="height: auto " >
                                <?php 
                                    echo '<option value="">Uso</option>';
                                    $tr = $con->Listar("SELECT * FROM gp_uso ORDER BY id_unico asc");
                                    for ($i = 0; $i < count($tr); $i++) {
                                       echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                    }
                                ?>
                            </select>
                        </div>
                            <div class="form-group form-inline  col-md-12 col-lg-12" style="margin-left: 5px; margin-top: 10px">
                                <label for="sector2" class="col-sm-5 control-label"></label>
                                <button type="submit" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-print" aria-hidden="true"></i></button>    
                            </div>
                        </div>
                    </div>
                </form>
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
    <script type="text/javascript"> 
            $("#p").select2();
            $("#s1").select2();
            $("#s2").select2();
            $("#uso").select2();
            
        </script>
    <?php require_once 'footer.php'?>  
</div>
</body>
</html>