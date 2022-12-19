<?php 
###############################MODIFICACIONES##########################
#16/05/2017 | ERICA G. | TERCEROS
//llamado a la clase de conexion
  require_once('Conexion/conexion.php');
  require_once 'head.php'; 
  $anno = $_SESSION['anno'];
  $compania = $_SESSION['compania'];?>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #sltrubi-error, #sltrubf-error, #fechaini-error, #fechafin-error, #sltTci-error, #sltTcf-error  {
    display: block;
    color: #155180;
    font-weight: normal;
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
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>

   <style>
    .form-control {font-size: 12px;}
    
</style>

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
<title>Auxiliares Comprobantes Presupuestales Gastos</title>
</head>
<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-top: -20px"> 
            <h2 align="center" class="tituloform">Auxiliares Comprobantes Presupuestales Gastos</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action=""  target=”_blank”>  
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
                    <div class="form-group">
                        <input type="hidden" name="headH" value="GASTOS" />
                        <input type="hidden" name="footH" value="Gastos" />
                        <?php 
                        $tituloH = "GASTOS";
                        $tituloF = "Gastos";     
                        $rubroI = "SELECT id_unico,codi_presupuesto, CONCAT(codi_presupuesto,' - ',nombre) AS rubro "
                                . "from gf_rubro_pptal WHERE parametrizacionanno = $anno AND (tipoclase = 7 OR tipoclase = 9 OR tipoclase=10 OR tipoclase=15 OR tipoclase=16) ORDER BY codi_presupuesto ASC";
                        $rsrubi = $mysqli->query($rubroI);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label for="sltrubi" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Rubro Inicial:</label>
                            <select required="required" name="sltrubi" id="sltrubi" class="select2_single form-control" title="Seleccione Rubro Inicial">
                                <option value>Rubro Inicial</option>
                                <?php while ($filarubi= mysqli_fetch_row($rsrubi)){ ?>
                                <option value="<?php echo $filarubi[1];?>"><?php echo ucwords($filarubi[2]);?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        <?php $rubroF = "SELECT id_unico,codi_presupuesto, CONCAT(codi_presupuesto,' - ',nombre) AS rubro "
                              . "from gf_rubro_pptal WHERE parametrizacionanno = $anno AND (tipoclase = 7 OR tipoclase = 9 OR tipoclase=10 OR tipoclase=15 OR tipoclase=16) ORDER BY codi_presupuesto DESC";
                            $rsrubf = $mysqli->query($rubroF);?>
                        <div class="form-group" style="margin-top: 0px">
                            <label for="sltrubf" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Rubro Final:</label>
                            <select required="required" name="sltrubf" id="sltrubf" class="select2_single form-control" title="Seleccione Rubro final">
                                <option value>Rubro Final</option>
                                <?php while ($filarubf = mysqli_fetch_row($rsrubf)) { ?>
                                <option value="<?php echo $filarubf[1];?>"><?php echo ucwords($filarubf[2]);?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        <div class="form-group" style="margin-top: 0px;">
                            <label for="fechaini" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Inicial:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="fechaini" id="fechaini" title="Ingrese Fecha Inicial">
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="fechafin" type = "date" class="col-sm-5 control-label"><strong class="obligado">*</strong>Fecha Final:</label>
                            <input required="required" class="col-sm-2 input-sm" type="text" name="fechafin" id="fechafin"  value="<?php echo date("Y-m-d");?>" title="Ingrese Fecha Final">
                        </div>
                        <?php $tci= "SELECT
                                tc.id_unico,
                                CONCAT(tc.codigo, ' - ', tc.nombre) AS compp,
                                tc.codigo
                            FROM
                                gf_tipo_comprobante_pptal tc
                            LEFT JOIN gf_clase_pptal cl ON
                                tc.clasepptal = cl.id_unico
                            WHERE
                                cl.tipoclase = 7 AND tc.compania = $compania 
                            ORDER BY
                                tc.codigo ASC";
                           $rsTci = $mysqli->query($tci);?> 
                        <div class="form-group" style="margin-top: -10px">
                            <label for="sltTci" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Comprobante Inicial:</label>
                            <select required="required" name="sltTci" id="sltTci" class="select2_single form-control" title="Seleccione Tipo Comprobante Inicial">
                                <option value>Tipo Comprobante Inicial</option>
                                <?php while ($filaTci = mysqli_fetch_row($rsTci)) { ?>
                                <option value="<?php echo $filaTci[2];?>"><?php echo ucwords($filaTci[1]);?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        <?php $tcf= "SELECT
                                tc.id_unico,
                                CONCAT(tc.codigo, ' - ', tc.nombre) AS compp,
                                tc.codigo
                            FROM
                                gf_tipo_comprobante_pptal tc
                            LEFT JOIN gf_clase_pptal cl ON
                                tc.clasepptal = cl.id_unico
                            WHERE
                                cl.tipoclase = 7 AND tc.compania = $compania 
                            ORDER BY
                                tc.codigo DESC";
                            $rsTcf = $mysqli->query($tcf);?>
                        <div class="form-group" style="margin-top: 0px">
                            <label for="sltTcf" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Comprobante Final:</label>
                            <select required name="sltTcf" id="sltTcf" class="select2_single form-control" title="Seleccione Tipo Comprobante Final">
                                <option value>Tipo Comprobante Final</option>
                                <?php while ($filaTci = mysqli_fetch_row($rsTcf)) { ?>
                                <option value="<?php echo $filaTci[2];?>"><?php echo ucwords($filaTci[1]);?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        <!--TERCERO-->
                        <?php $ti= "SELECT IF(CONCAT_WS(' ',
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
                                    tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.id_unico 
                                    FROM gf_tercero tr WHERE tr.compania = $compania  ORDER BY tr.numeroidentificacion ASC";
                           $rsTi = $mysqli->query($ti);?> 
                        <div class="form-group" style="margin-top: 0px">
                            <label for="sltTi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tercero Inicial:</label>
                            <select  name="sltTi" id="sltTi" class="select2_single form-control" title="Seleccione Tercero Inicial">
                                <option value>Tercero Inicial</option>
                                <?php while ($filaTi = mysqli_fetch_row($rsTi)) { ?>
                                <option value="<?php echo $filaTi[1];?>"><?php echo $filaTi[1].' - '.ucwords(mb_strtolower($filaTi[0]));?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        <?php $tf= "SELECT IF(CONCAT_WS(' ',
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
                                    tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.id_unico 
                                    FROM gf_tercero tr  WHERE tr.compania = $compania ORDER BY tr.numeroidentificacion DESC";
                            $rsTf = $mysqli->query($tf);?>
                        <div class="form-group" style="margin-top: 0px">
                            <label for="sltTf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tercero Final:</label>
                            <select  name="sltTf" id="sltTf" class="select2_single form-control" title="Seleccione Tercero Final">
                                <option value>Tercero Final</option>
                                <?php while ($filaTFI = mysqli_fetch_row($rsTf)) { ?>
                                <option value="<?php echo $filaTFI[1];?>"><?php echo $filaTFI[1].' - '.ucwords(mb_strtolower($filaTFI[0]));?></option>                                
                                <?php } ?>                                    
                            </select>
                        </div>
                        
                        <div class="form-group" style="margin-top: 0px">
                            <label for=tipo" class="control-label col-sm-5"><strong class="obligado"></strong>Informe por:</label>
                            <select  name="tipo" id="tipo" class="select2_single form-control"  title="Seleccione Tipo Informe" >
                                <option value="0">Informe</option>
                                <option value="1">Auxiliar Comprobantes Presupuestales</option>
                                <option value="2">Listado de Comprobantes Presupuestales</option>
                                <option value="3">Auxiliar por Terceros</option>
                                <option value="4">Listado Comprobantes Abiertos</option>
                                
                            </select> 
                        </div>
                        <div class="form-group text-center" style="margin-top:20px;">
                            <div class="col-sm-1" style="margin-top:0px;margin-left:620px">
                                <button onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>              
                            </div>
                            <div class="col-sm-1" style="margin-top:-34px;margin-left:670px">
                                <button onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>     
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
function reportePdf(){
    var opcion = document.getElementById('tipo').value;
    switch(opcion){
        case('0'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_AUX_PPTALES_GASTOS.php');
        break;
        case('1'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_AUX_COMPROBANTES.php');
        break;
        case('2'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_LISTADO_COMPROBANTES_PPTALES.php');
        break;
        case('3'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_AUXILIAR_TERCEROS.php');
        break;
        case('4'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_LISTADO_COMPROBANTES_ABIERTOS.php');
        break;
    }
    
}
</script>
<script>
function reporteExcel(){
    var opcion = document.getElementById('tipo').value;
    switch(opcion){
        case('0'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_AUX_PPTALES_GASTOS_EXCEL.php');
        break;
        case('1'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_AUX_COMPROBANTES_EXCEL.php');
        break;
        case('2'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_LISTADO_COMPROBANTES_PPTALES_EXCEL.php');
        break;
        case('3'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_AUXILIAR_TERCEROS_EXCEL.php');
        break;
        case('4'):
            $('form').attr('action', 'informes/INFORMES_PPTAL/generar_INF_LISTADO_COMPROBANTES_ABIERTOS_EXCEL.php');
        break;
    }
    
}

</script>
</body>
</html>
<?php require_once 'footer.php'?>   