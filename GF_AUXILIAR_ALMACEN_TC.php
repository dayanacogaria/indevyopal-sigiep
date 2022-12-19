<?php 
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
require_once 'head.php'; 

$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$con        = new ConexionPDO();
 ?>
<title>Auxiliares Almacén Por Tipo</title>
</head>
<body>

<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltTci-error, #sltTcf-error, #fechaini-error, #fechafin-error, #sltctai-error, #sltctaf-error  {
    display: block;
    color: #bd081c;
    font-weight: bold;
    font-style: italic;
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

<script>
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
            yearSuffix: '',
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fechaini").datepicker({changeMonth: true,}).val(fecAct);
        $("#fechafin").datepicker({changeMonth: true}).val(fecAct);
        
        
});
</script>
<!-- contenedor principal -->  
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Auxiliares Almacén Por Tipo Movimiento </h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltTci" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Movimiento Inicial:</label>
                            <select  name="sltTci" id="sltTci" class="select2_single form-control" title= "Seleccione Tipo Movimiento inicial" style="height: 30px" required>
                                <option value="">Tipo Movimiento Inicial</option>
                                <?php 
                                $tci= "SELECT DISTINCT t.id_unico, t.sigla, t.nombre 
                                FROM gf_movimiento m 
                                LEFT JOIN gf_tipo_movimiento t ON m.tipomovimiento = t.id_unico 
                                WHERE t.compania = $compania ORDER BY t.sigla ASC";
                                $rsTci = $mysqli->query($tci);
                                while ($filaTci = mysqli_fetch_row($rsTci)) { ?>
                                    <option value="<?php echo $filaTci[1];?>"><?php echo $filaTci[1].' - '.ucwords(mb_strtolower($filaTci[2]));?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltTcf" class="control-label col-sm-5"><strong class="obligado">*</strong>Tipo Movimiento Final:</label>
                            <select name="sltTcf" class="select2_single form-control" id="sltTcf" title="Seleccione Tipo Movimiento final" style="height: 30px"  required >
                                <option value="">Tipo Movimiento Final</option>
                                <?php 
                                $tcf= "SELECT DISTINCT t.id_unico, t.sigla, t.nombre 
                                FROM gf_movimiento m 
                                LEFT JOIN gf_tipo_movimiento t ON m.tipomovimiento = t.id_unico 
                                WHERE t.compania = $compania ORDER BY t.sigla DESC";
                                $rsTcf = $mysqli->query($tcf);
                                while ($filaTcf = mysqli_fetch_row($rsTcf)) { ?>
                                <option value="<?php echo $filaTcf[1];?>"><?php echo ($filaTcf[1].' - '. ucwords(mb_strtolower($filaTcf[2]))); ?></option>
                                <?php } ?>
                            </select>   
                        </div>
                        <div class="form-group" style="margin-top: -5px;">
                             <label for="fechafin" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                             <input class="form-control" type="text" name="fechafin" id="fechafin"  value="<?php echo date("Y-m-d");?>" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px">
                            <label for="sltctai" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Elemento Inicial:</label>
                            <select name="sltctai" id="sltctai" required style="height: auto" class="select2_single form-control" title="Seleccione Elemento inicial">
                                <option value="">Elemento Inicial</option>
                                <?php  $cuentaI = "SELECT DISTINCT p.codi, CONCAT(p.codi,' - ',p.nombre) 
                                    FROM gf_detalle_movimiento dm 
                                    LEFT JOIN gf_plan_inventario p ON dm.planmovimiento = p.id_unico 
                                    WHERE p.compania = $compania ORDER BY p.codi ASC";
                                $rsctai = $mysqli->query($cuentaI);
                                while ($filactai= mysqli_fetch_row($rsctai)) {  ?>
                                <option value="<?php echo $filactai[0];?>"><?php echo ucwords(mb_strtolower($filactai[1]));?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>   
                        <div class="form-group" style="margin-top: 0px">
                            <label for="sltctaf" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Elemento Final:</label>
                            <select name="sltctaf" id="sltctaf" style="height: auto" class=" select2_single form-control" title= "Seleccione Elemento final" required>
                               <option value="">Elemento Final</option>
                           <?php 
                            $cuentaF = "SELECT DISTINCT p.codi, CONCAT(p.codi,' - ',p.nombre) 
                                    FROM gf_detalle_movimiento dm 
                                    LEFT JOIN gf_plan_inventario p ON dm.planmovimiento = p.id_unico 
                                    WHERE p.compania = $compania ORDER BY p.codi DESC";
                            $rsctaf = $mysqli->query($cuentaF);
                            while ($filactaf = mysqli_fetch_row($rsctaf)) { ?>
                               <option value="<?php echo $filactaf[0];?>"><?php echo ucwords(mb_strtolower($filactaf[1]));?></option>                                
                            <?php  } ?>                                    
                           </select>
                       </div>
                        <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >
                            <button style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
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
    <script>
    function reporteExcel(){
       $('form').attr('action', 'informes_almacen/INF_AUXILIAR_TIPO.php');
    }
    </script>
</div>
<?php require_once 'footer.php' ?>  
</body>
</html>