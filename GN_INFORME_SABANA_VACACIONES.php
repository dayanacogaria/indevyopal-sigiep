<?php
require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
$vig = $_SESSION['anno'];
?>
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <style>
        label #sltPeriodo-error {
            display: block;
            color: #bd081c;
            font-weight: bold;
            font-style: italic;
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
    <title>Sábana de Vacaciones</title>
    <link href="css/select/select2.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Sábana de Vacaciones</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target="_blank">
                        <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <?php
                        $uniE = " SELECT id_unico, nombre FROM gn_unidad_ejecutora";
                        $Ueje = $mysqli->query($uniE);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <div class="clasUni" style="margin-top: -5px">
                                <label for="sltUnidadE" class="control-label col-sm-5" style="margin-top: 0px">
                                    Unidad Ejecutora:
                                </label>
                                <div class="col-sm-5 col-md-5 col-lg-5">
                                    <select name="sltUnidadE" id="sltUnidadE" title="Seleccione Unidad Ejecutora" style="height: 30px; " class="select2_single form-control" >
                                        <option value="">Unidad Ejecutora</option>
                                        <?php
                                        while($rowU = mysqli_fetch_row($Ueje)){
                                            echo "<option value=".$rowU[0].">".$rowU[1]."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <?php
                        $per = "SELECT  id_unico, codigointerno FROM gn_periodo
                                WHERE   id_unico            != 1 
                                and tipoprocesonomina = 7 
                                AND     parametrizacionanno = '$vig'";
                        $periodo = $mysqli->query($per);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltPeriodo" class="control-label col-sm-5" style="margin-top: 0px; "><strong class="obligado">*</strong>Periodo:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <select  name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="height: 30px;" class="select2_single form-control" required="required" >
                                    <option value="">Periodo</option>
                                    <?php
                                    while($rowE = mysqli_fetch_row($periodo)){
                                        echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                       
                        <div class="form-group" style="margin-top: -5px;">
                            <label for="chkTipoA" class="control-label col-sm-5 col-md-5 col-lg-5">Tipo Archivo:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <label for="" class="label-inradio"><input name="chkTipoA" type="radio" id="optPdf" >PDF</label>
                                <label for="" class="label-inradio"><input name="chkTipoA" type="radio" id="optExcel" checked="checked">EXCEL</label>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button id="enviar"  class="btn sombra btn-primary" title="Generar reporte PDF" style="margin-top: 15px;"><i>Generar</i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require_once './footer.php'; ?>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="js/select/select2.full.js"></script>
    <script>
        $("#sltPeriodo").select2();
        $("#sltUnidadE").select2();
        
        
        $("#enviar").click(function(){
            if($("#optPdf").is(':checked')){
                $("#form").attr("action", "informes_nomina/generar_INF_SABANA_VACACIONES.php?t=1");
            } else {
                if($("#optExcel").is(":checked")){
                    $("#form").attr("action", "informes_nomina/generar_INF_SABANA_VACACIONES.php?t=2");
                }
            }
        });
    </script>
</body>
</html>