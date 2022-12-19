<?php

require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
#session_start();
$vig = $_SESSION['anno'];
@$id = $_GET['emp'];
@$vo = $_GET['vol'];
@$peri = $_GET['per'];
$emp = "SELECT e.id_unico, e.tercero, CONCAT_WS( ' ', t.nombreuno, t.nombredos, t.apellidouno,t.apellidodos ) , t.tipoidentificacion, ti.id_unico, CONCAT(ti.nombre,' ',t.numeroidentificacion)
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
    
}

$PE = "SELECT  p.id_unico, CONCAT(p.codigointerno,' - ',tpn.nombre) 
FROM gn_periodo p LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico 
WHERE md5(p.id_unico) = '$peri'";
$PERIO = $mysqli->query($PE);
$MES = mysqli_fetch_row($PERIO);

$id_P = $MES[0];
$nom_pe = $MES[1];
if(empty($id_P)){
$Prdo = "PERIODO";
}else{
$Prdo = $nom_pe;
}

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
               
        $("#sltFechaA").datepicker({changeMonth: true,}).val();
        $("#sltFechaI").datepicker({changeMonth: true,}).val();
        $("#sltFechaF").datepicker({changeMonth: true,}).val();
        $("#sltFechaID").datepicker({changeMonth: true,}).val();
        $("#sltFechaFD").datepicker({changeMonth: true,}).val();
        
        
});
</script>
   <title>Liquidación Cesantias</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 text-left" style="margin-top: 0px">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Liquidación de Cesantías</h2>
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonNomina/gn_Liquidacion_Cesantias.php?t=1">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                           
                            <div class="form-group form-inline" style="margin-top:-25px">
                                <?php 
                                    if(empty($idT))
                                    {
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
                                    }
                                    else
                                    {
                                        $emp = "SELECT                      
                                                        e.id_unico,
                                                        e.tercero,
                                                        t.id_unico,
                                                        CONCAT_WS( ' ', t.nombreuno, t.nombredos, t.apellidouno,t.apellidodos )
                                            FROM gn_empleado e
                                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico = 0";
                                        $idTer = $idT;
                                    }
                                    $empleado = $mysqli->query($emp);
                                ?>
                                <label for="sltEmpleado" class="col-sm-2 control-label"><strong class="obligado">*</strong>Empleado:</label>
                                <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 160px;height: 30px" class="form-control col-sm-1">
                                    <option value="<?php echo $idTer?>"><?php echo $tercero?></option>
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
                                    if(empty($id_P)){
                                        $per = "SELECT  p.id_unico, CONCAT(p.codigointerno,' - ',tpn.nombre) "
                                        . "FROM gn_periodo p LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico "
                                        . "WHERE p.id_unico!=1  AND p.tipoprocesonomina = 11 AND p.parametrizacionanno = '$vig'";

                                    }else{
                                        $per = "SELECT  p.id_unico, CONCAT(p.codigointerno,' - ',tpn.nombre) "
                                        . "FROM gn_periodo p LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico "
                                        . "WHERE p.id_unico = $id_P ";

                                    }

                                    $periodo = $mysqli->query($per);
                                ?>

                                <label for="sltPeriodo" class="col-sm-2 control-label"><strong class="obligado">*</strong>Periodo:</label>
                                <select required="required" name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="width: 150px;height: 30px" class="form-control col-sm-1">
                                <option value="<?php echo $id_P ?>"><?php echo $Prdo ?></option>
                                    <?php 
                                        while($rowE = mysqli_fetch_row($periodo))
                                        {
                                            echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                        }
                                    ?>       
                                </select>
                                <label for="No" class="col-sm-1 control-label"></label>
                                <button type="submit" id="liquidar"  title ="Liquidar"  class="btn btn-primary shadow" ><li   class="glyphicon glyphicon-usd" ></li></button>
                                <button type = "button" id="btnSabana" class = "btn btn-primary shadow" title="Imprimir Sábana" style="margin-left: 4px;" disabled="true"><li class="glyphicon glyphicon-equalizer"></li></button>           
                            </div>
                        </form>  
                    </div>
                    <?php   if(!empty($vo)){?>
                            <script type="text/javascript">
                               
                                $('#btnSabana').prop("disabled",false);
                            </script>
                <?php   } ?>  
            </div>
        </div>
    </div>
    
    <?php require_once './footer.php'; ?>

</body>
<script type="text/javascript" src="js/select2.js"></script>
<script type="text/javascript"> 
    $("#sltEmpleado").select2();
    $("#sltPeriodo").select2();
</script>
 <script src="js/md5.js"></script>
    <script type="text/javascript">
        $("#btnImprimir").click(function()
        {
            var idemp = $("#sltEmpleado").val();
            var idper = $("#sltPeriodo").val();
            
            window.open('informes/generar_INF_VOLANTE_CESANTIAS.php?id_emp='+idemp+'&id_per='+md5(idper));
            
            
        });
        
    </script>  
    
    <script type="text/javascript">
        $("#btnSabana").click(function(){
            var idemp = $("#sltEmpleado").val();
            var idper = $("#sltPeriodo").val();
            var X = 1;
            var grupo = "";
            var unidad = "";
            window.open('informes/generar_INF_SABANA_CESANTIAS.php?id_per='+idper+'&x='+X+'&gr='+grupo+'&un='+unidad);
        });
    </script>


    
    
</html>


       