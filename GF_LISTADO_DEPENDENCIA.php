<?php
require 'head.php';
require 'Conexion/conexion.php';
$compania = $_SESSION['compania'];
?>
<title>Auxiliar Movimiento Por Dependencia General</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="css/datapicker.css">
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" type="text/css" href="css/bootstrap-notify.css">
<link rel="stylesheet" type="text/css" href="css/font-awesome.css">
<script src="dist/jquery.validate.js"></script>
<style>
    label #txtFechaFinal-error, #sltDepInicial-error, #sltDepFinal-error{
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;
}
</style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require('menu.php'); ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px; margin-top: 0px;">Auxiliar Movimiento Por Dependencia General</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  target=”_blank” enctype="multipart/form-data" action="">
                        <p align="center" style="margin-bottom: 5px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group" >
                            <label for="txtFechaFinal" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Fecha Final:</label>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <input type="text" class="form-control" id="txtFechaFinal" name="txtFechaFinal" placeholder="Fecha Final" required="" title="Seleccione Fecha" style="width:58%" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group dependencia" style="margin-top: -10px">
                            <label for="sltDepInicial" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Dependencia Inicial:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <select name="sltDepInicial" id="sltDepInicial" class="select2 form-control text-left" required="required">
                                <?php
                                $html = "";
                                $sql  = "SELECT id_unico, nombre, sigla FROM gf_dependencia WHERE compania = $compania ORDER BY id_unico ASC";
                                $res  = $mysqli->query($sql);
                                while($row = mysqli_fetch_row($res)){
                                    $html .= "<option value=\"$row[0]\">$row[2] ".ucwords(mb_strtolower($row[1]))."</option>";
                                }
                                echo $html;
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group dependencia">
                            <label for="sltDepFinal" class="control-label col-sm-5 col-md-5 col-lg-5"><strong class="obligado">*</strong>Dependencia Final:</label>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <select name="sltDepFinal" id="sltDepFinal" class="select2 form-control text-left" required="required">
                                <?php
                                $html = "";
                                $sql  = "SELECT id_unico, nombre, sigla FROM gf_dependencia WHERE compania = $compania ORDER BY id_unico DESC";
                                $res  = $mysqli->query($sql);
                                while($row = mysqli_fetch_row($res)){
                                    $html .= "<option value=\"$row[0]\">$row[2] ".ucwords(mb_strtolower($row[1]))."</option>";
                                }
                                echo $html;
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12 col-md-12 col-lg-12 text-left">
                                <label for="" class="control-label col-sm-5 col-md-5 col-lg-5"></label>
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                                    <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                                    
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require('footer.php'); ?>
    <script src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <script src="js/bootstrap-notify.js"></script>
    <script type="text/javascript" src="js/md5.js"></script>
    <script>
        $(".select2").select2();
         $(function(){
            var fecha = new Date();
            var dia = fecha.getDate();
            var mes = fecha.getMonth() + 1;
            if(dia < 10){
                dia = "0" + dia;
            }
            if(mes < 10){
                mes = "0" + mes;
            }
            var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
            $.datepicker.regional['es'] = {
                closeText: 'Cerrar',
                prevText: 'Anterior',
                nextText: 'Siguiente',
                currentText: 'Hoy',
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
                dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
                dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);
            $("#txtFechaInicial").datepicker({changeMonth: true}).val();
            $("#txtFechaFinal").datepicker({changeMonth: true}).val();
        });

        $("#txtFechaFinal").change(function(){
            var fechaInicial = $("#txtFechaInicial").val();
            var fechaFinal   = $("#txtFechaFinal").val();

            if(fechaFinal < fechaInicial){
                $("#txtFechaFinal").parents(".col-sm-5").addClass("has-error").removeClass('has-success');
                $("#txtFechaFinal").val("");
            }else{
                $("#txtFechaFinal").parents(".col-sm-5").addClass("has-success").removeClass('has-error');
            }
        });
    </script>
    <script>
    $().ready(function() {
      var validator = $("#form").validate({
            ignore: "",
        errorPlacement: function(error, element) {
          $( element )
            .closest( "form" )
              .find( "label[for='" + element.attr( "id" ) + "']" )
                .append( error );
        }
      });
      $(".cancel").click(function() {
        validator.resetForm();
      });
    });
    
    function reportePdf(){
        $('form').attr('action', 'informes_almacen/INF_LISTADO_DEPENDENCIA.php?t=1');
    } 
    function reporteExcel(){
        $('form').attr('action', 'informes_almacen/INF_LISTADO_DEPENDENCIA.php?t=2');
    } 
  </script>
</body>
</html>