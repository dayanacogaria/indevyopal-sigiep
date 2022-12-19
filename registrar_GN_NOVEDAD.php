<?php
require_once ('./Conexion/conexion.php');
require_once ('head_listar.php');
#session_start();
@$id = $_GET['idE'];
@$period = $_GET['periodo'];
@$proceso = $_GET['proceso'];
@$aplic = $_GET['apli'];
$apli = $aplic;
$idTer="";

if(empty($aplic)){
  $apli = 4;
  $nreg = 0;
}else{
  $apli = $aplic;  
}

$emp = "SELECT e.id_unico, e.tercero, CONCAT_WS(' ', t.nombreuno, t.nombredos,  t.apellidouno,t.apellidodos ) , t.tipoidentificacion, ti.id_unico, CONCAT(ti.nombre,' ',t.numeroidentificacion)
FROM gn_empleado e
LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
WHERE e.id_unico = '$id'";
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
    $a="inline-block";
}
$per = "SELECT id_unico, codigointerno FROM gn_periodo WHERE id_unico = $period";

$p  = $mysqli->query($per);
$pe = mysqli_fetch_row($p);

$pid = $pe[0];
$pde = $pe[1];
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<style >
   table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
   table.dataTable tbody td,table.dataTable tbody td{padding:1px}
   .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
       font-family: Arial;}
   .shadow{
       box-shadow: 1px 1px 1px 1px gray;
   }
</style>
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
               
        $("#sltFecha").datepicker({changeMonth: true,}).val();
       
        
        
});
</script>
<script>
    function elegir(value) {
          if(value=="1")
      {
      
        document.getElementById("sltEmpleado").disabled=true;
      }else if(value=="2"){
      
        document.getElementById("sltEmpleado").disabled=true;
      }else if(value=="3"){
        
        document.getElementById("sltEmpleado").disabled=false;
      }else if(value=="4"){
        document.getElementById("sltEmpleado").disabled=false;
      }
   }

   
</script>

<title>Registrar Novedad</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
</head>
<body>
         
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 text-left" style="margin-top: 0px">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Novedad</h2>
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarNovedadJson.php" style="margin-bottom: -15px">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p><!--------------------------------------------------------------------------------------------------------------------- -->
                            <div class="col-sm-4 col-md-4 col-lg-4 form-group" style="margin-top: -22px; margin-left: 20px" name="aplica" id="aplica">
                                <label for="aplicabilidad" class="col-sm-1 col-md-1 col-lg-1 control-label" style="margin-top: -12px "><strong style="color:#03C1FB;"  >*</strong>Aplicabilidad:</label>
                                <br>
                                <?php if($apli == 1){ ?>
                                    <input type="radio" name="aplicabilidad" id="apli1" value="1" onchange ="elegir(this.value); deshabilitar();"  required="required" checked> Siempre para todos los empleados
                                    <br>
                                    <input type="radio" name="aplicabilidad" id="apli2" value="2" onchange ="elegir(this.value); deshabilitar();" > Al periodo para todos los empleados
                                    <br>
                                    <input type="radio" name="aplicabilidad" id="apli3" value="3" onchange ="elegir(this.value);" > Siempre para un empleado
                                    <br>                                        
                                    <input type="radio" name="aplicabilidad" id="apli4" value="4" onchange ="elegir(this.value);"  > Al periodo para un empleado
                                <?php }elseif($apli == 2){ ?>

                                    <input type="radio" name="aplicabilidad"  id="apli1" value="1" onchange ="elegir(this.value); deshabilitar();"  required="required"> Siempre para todos los empleados
                                    <br>
                                    <input type="radio" name="aplicabilidad" id="apli2" value="2" onchange ="elegir(this.value); deshabilitar();"  checked> Al periodo para todos los empleados
                                    <br>
                                    <input type="radio" name="aplicabilidad" id="apli2" value="3" onchange ="elegir(this.value);" > Siempre para un empleado
                                    <br>                                        
                                    <input type="radio" name="aplicabilidad" id="apli4" value="4" onchange ="elegir(this.value);"  > Al periodo para un empleado
                                <?php }elseif($apli == 3){ ?>
                                    <input type="radio" name="aplicabilidad" id="apli1" value="1" onchange ="elegir(this.value); deshabilitar();"  required="required"> Siempre para todos los empleados
                                    <br>
                                    <input type="radio" name="aplicabilidad" id="apli2" value="2" onchange ="elegir(this.value); deshabilitar();"  > Al periodo para todos los empleados
                                    <br>
                                    <input type="radio" name="aplicabilidad" id="apli3" value="3" onchange ="elegir(this.value);" checked> Siempre para un empleado
                                    <br>                                        
                                    <input type="radio" name="aplicabilidad" id="apli4" value="4" onchange ="elegir(this.value);"  > Al periodo para un empleado
                                <?php }elseif($apli == 4){ ?>
                                    <input type="radio" name="aplicabilidad" id="apli1" value="1" onchange ="elegir(this.value); deshabilitar();"  required="required"> Siempre para todos los empleados
                                    <br>
                                    <input type="radio" name="aplicabilidad" id="apli2" value="2" onchange ="elegir(this.value); deshabilitar();"  > Al periodo para todos los empleados
                                    <br>
                                    <input type="radio" name="aplicabilidad" id="apli3" value="3" onchange ="elegir(this.value);" > Siempre para un empleado
                                    <br>                                        
                                    <input type="radio" name="aplicabilidad" id="apli4" value="4" onchange ="elegir(this.value);" checked > Al periodo para un empleado
                                <?php } ?>
                            </div>
                            <div class="col-sm-8 col-md-8 col-lg-8 form-group">
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <input type="hidden" class="hidden" id="txtIdper" name="txtIdper" value="<?php echo $pid; ?>">
                                    <?php
                                    $per = "SELECT id_unico, codigointerno FROM gn_periodo WHERE id_unico!=$pid AND id_unico !=1";
                                    $period = $mysqli->query($per);
                                    ?>
                                    <label for="sltPeriodo" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Período:</label>
                                    <input  name="txtPeriodo" id="txtPeriodo" title="" type="text"  readonly="readonly" style="width: 25%;height: 34px" class="form-control col-sm-2 col-md-2 col-lg-2" value="<?php echo $pde; ?>">
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
                                                    WHERE et.id_unico IS NOT NULL AND e.id_unico != $idT";
                                        $idTer = $idT;
                                    }
                                    $empleado = $mysqli->query($emp);
                                    ?>
                                    <label for="sltEmpleado" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado">*</strong>Empleado:</label>
                                    <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 40%;height: 34px;margin-left: 5px" class="form-control col-sm-2 col-md-2 col-lg-2" onchange="javascript:habilitar();" >
                                        <?php
                                        echo "<option value=\"$idTer\">$tercero</option>";
                                        while($rowE = mysqli_fetch_row($empleado))
                                        {
                                            echo "<option value=".$rowE[0].">".$rowE[3]."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <script type="text/javascript">
                                    $("#sltEmpleado").click(function(){
                                        var per = $("#txtIdper").val();
                                        var emp = $("#sltEmpleado").val();
                                        var apl = "";
            
                                        $('input[name="aplicabilidad"]:checked').each(function() {
                                            apl = $(this).val();
                
                                        });
                                        document.location = 'registrar_GN_NOVEDAD.php?periodo='+per+'&idE='+emp+'&apli='+apl; 
                                    });

                                    $("#apli1").click(function(){
                                  
                                        var per = $("#txtIdper").val();
                                        var emp = "";
                                        var apl = $("#apli1").val();
                                        document.location = 'registrar_GN_NOVEDAD.php?periodo='+per+'&idE='+emp+'&apli='+apl; 
                                    
                                    });
                                
                                    $("#apli2").click(function(){
                                  
                                        var per = $("#txtIdper").val();
                                        var emp = "";
                                        var apl = $("#apli2").val();
                                        document.location = 'registrar_GN_NOVEDAD.php?periodo='+per+'&idE='+emp+'&apli='+apl; 
                                    
                                    });

                                    $("#apli3").click(function(){
                                  
                                        var per = $("#txtIdper").val();
                                        var emp = $("#sltEmpleado").val();
                                        var apl = $("#apli3").val();

                                        if(emp.length !== 0){ 
                                            document.location = 'registrar_GN_NOVEDAD.php?periodo='+per+'&idE='+emp+'&apli='+apl; 
                                        } 
                                    });

                                    $("#apli4").click(function(){
                                  
                                        var per = $("#txtIdper").val();
                                        var emp = $("#sltEmpleado").val();
                                        var apl = $("#apli4").val();

                                        if(emp.length !== 0){ 
                                            document.location = 'registrar_GN_NOVEDAD.php?periodo='+per+'&idE='+emp+'&apli='+apl;
                                        } 
                                    });  
                                
                                </script>
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <?php
                                    $con = "SELECT c.id_unico, CONCAT(c.codigo,' - ',c.descripcion),c.unidadmedida , u.id_unico, u.nombre FROM gn_concepto c
                                            LEFT JOIN gn_unidad_medida_con u ON c.unidadmedida = u.id_unico";
                                    $concept = $mysqli->query($con);
                                    ?>
                                    <label for="sltConcepto" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Concepto:</label>
                                    <select name="sltConcepto" id="sltConcepto" title="Seleccione Concepto" style="width: 25%;height: 34px" class="form-control col-sm-1 col-md-1 col-lg-1" >
                                        <option value="">Concepto</option>
                                        <?php
                                        while($rowC = mysqli_fetch_row($concept))
                                        {
                                            echo "<option value=".$rowC[0].">".$rowC[1]. "</option>";

                                        }
                                        ?>
                                    </select>
                                    <script type="text/javascript">

                                        $("#sltConcepto").change(function(){
                                            var con=$("#sltConcepto").val();
                                            $.ajax({
                                                type:"GET",
                                                url:"traerUnidad.php?id="+con,
                                                success: function (data) {
                                                    result = JSON.parse(data);
                                                    $("#txtuni").val(result);
                                                }
                                            });
                                        });
                                    </script>
                                    <label for="txtValor" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Valor:</label>
                                    <input  name="txtValor" id="txtValor" title="Ingrese Valor" type="text" style="width: 40%;height: 34px;margin-left: 5px" class="form-control col-sm-1 col-md-1 col-lg-12" placeholder="Valor" onkeypress="return txtValida(event,'dec', 'valor', '2');" onkeyup="formatC('txtValor');">
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <?php /*
                                        $sq = "SELECT c.id_unico, c.descripcion, u.id_unico, u.nombre
                                                FROM gn_concepto c
                                                LEFT JOIN gn_unidad_medida_con u ON c.unidadmedida = u.id_unico
                                                WHERE c.id_unico = $rowC[0]";
                                    */
                                    ?>
                                    <label for="txtuni" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Unidad M:</label>
                                    <input readonly="readonly" name="txtuni" id="txtuni" title="" type="text" style="width: 25%;height: 34px" class="form-control col-sm-1 col-md-1 col-lg-1" value="<?php echo $rowC[4] ?>">
                                    <!----------Script para invocar Date Picker-->
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            $("#datepicker").datepicker();
                                        });
                                    </script>
                                    <label for="sltFecha"  type="date" class="col-sm-2 control-label">
                                        <strong class="obligado"></strong>Fecha:
                                    </label>
                                    <input name="sltFecha" id="sltFecha" title="Ingrese Fecha " type="text" style="width: 40%;height: 34px;margin-left: 5px;" class="form-control col-sm-1 col-md-1 col-lg-1"   placeholder="Ingrese la fecha">
                                </div>
                            </div>
                            <!--disabled="true"-->
                            <div class="col-sm-4 col-md-4 col-lg-4 form-group col-sm-push-10 col-md-push-10 col-lg-push-10" style="
                                 margin-left: -950px!important; margin-top: 150px!important;">
                                 
                                
                                <button type="submit" class="btn btn-primary shadow" ><li class="glyphicon glyphicon-floppy-disk" ></li></button>
                                <label for="No" class="col-sm-1 control-label"></label>
                                <button type="button" id="liquidar"  title ="Liquidar"  class="btn btn-primary shadow" ><li   class="glyphicon glyphicon-usd" ></li></button>
                                <button type="button" id="btnImprimir" class="btn btn-primary shadow" title="Imprimir Volante" >
                                        <li class="glyphicon glyphicon-print"></li>
                                    </button>
                                <button type="button" id="btnCalavera" class="btn btn-primary shadow" style="background: #f60303; color: #fff; border-color: #f60303; width: 50px" title="Eliminar Novedades"><img src="img/eliminar.png" style="width: 100%" onclick="calavera();" ></button>
                            </div>
                        </form>    
                    </div>
            </div>
            <div class="col-sm-2 col-md-2 col-lg-2" style="margin-top:-22px">
                <table class="tablaC table-condensed text-center" align="center">
                    <thead>
                    <tr>
                    <tr>
                        <th>
                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <a class="btn btn-primary btnInfo" href="registrar_GN_EMPLEADO.php">EMPLEADO</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="btn btn-primary btnInfo" href="registrar_GN_CONCEPTO.php">CONCEPTO</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="btn btn-primary btnInfo" href="registrar_GN_PERIODO.php">PERÍODO</a>
                        </td>
                    </tr>
                </table>
            </div>
                    <!-- habilita el boton  de liquidar nómina para un empleado -->
                    <?php   if(!empty($_GET['idE'])){ ?>
                                <script type="text/javascript">
                                    $("#liquidar").prop("disabled", false);
                                </script>
                                 <script type="text/javascript">
                                    $("#btnImprimir").prop("disabled", false);
                                </script>
                                 <script type="text/javascript">
                                    $("#btnCalavera").prop("disabled", false);
                                </script>
                    <?php   }else{ ?>
                                <script type="text/javascript">
                                    $("#liquidar").prop("disabled", true);
                                </script>
                                <script type="text/javascript">
                                    $("#btnImprimir").prop("disabled", true);
                                </script>
                                <script type="text/javascript">
                                    $("#btnCalavera").prop("disabled", true);
                                </script>
                    <?php   } ?>

<!---------------------------------------------------------------------------------------------------->                        
                    <div class="form-group form-inline" style="margin-top:5px; " >
                        <?php

                            switch ($apli){

                                case 1:

                                    $sql = "SELECT          n.id_unico,
                                             n.valor,
                                             n.fecha,
                                             n.empleado,
                                             e.id_unico,
                                             e.tercero,
                                             t.id_unico,
                                             CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                                             n.periodo,
                                             p.id_unico,
                                             p.codigointerno,
                                             n.concepto,
                                             c.id_unico,
                                             CONCAT(c.codigo,' - ',c.descripcion),
                                              c.codigo
                                             FROM gn_novedad n    
                                             LEFT JOIN   gn_empleado e ON n.empleado = e.id_unico
                                             LEFT JOIN   gf_tercero t ON e.tercero = t.id_unico
                                             LEFT JOIN   gn_periodo p ON n.periodo = p.id_unico
                                             LEFT JOIN gn_concepto c ON n.concepto = c.id_unico
                                             WHERE  n.aplicabilidad = $apli AND n.empleado=2 AND n.periodo = 1 
                                             ORDER BY  c.codigo ASC";
                                    $resultado = $mysqli->query($sql);
                                    $nreg = mysqli_num_rows($resultado);
                                break;
                                case 2:

                                    $sql = "SELECT          n.id_unico,
                                             n.valor,
                                             n.fecha,
                                             n.empleado,
                                             e.id_unico,
                                             e.tercero,
                                             t.id_unico,
                                             CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                                             n.periodo,
                                             p.id_unico,
                                             p.codigointerno,
                                             n.concepto,
                                             c.id_unico,
                                             CONCAT(c.codigo,' - ',c.descripcion),
                                             c.codigo
                                             FROM gn_novedad n    
                                             LEFT JOIN   gn_empleado e ON n.empleado = e.id_unico
                                             LEFT JOIN   gf_tercero t ON e.tercero = t.id_unico
                                             LEFT JOIN   gn_periodo p ON n.periodo = p.id_unico
                                             LEFT JOIN gn_concepto c ON n.concepto = c.id_unico
                                             WHERE  n.aplicabilidad = $apli AND n.empleado IS NULL AND n.periodo = $pid
                                             ORDER BY c.codigo ASC";
                                    $resultado = $mysqli->query($sql);
                                    $nreg = mysqli_num_rows($resultado);
                                break;
                                case 3:
                                    if(!empty($idTer)){
                                        $sql = "SELECT          n.id_unico,
                                                                n.valor,
                                                                n.fecha,
                                                                n.empleado,
                                                                e.id_unico,
                                                                e.tercero,
                                                                t.id_unico,
                                                                CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                                                                n.periodo,
                                                                p.id_unico,
                                                                p.codigointerno,
                                                                n.concepto,
                                                                c.id_unico,
                                                                CONCAT(c.codigo,' - ',c.descripcion),
                                                                c.codigo
                                                FROM gn_novedad n    
                                                LEFT JOIN   gn_empleado e ON n.empleado = e.id_unico
                                                LEFT JOIN   gf_tercero t ON e.tercero = t.id_unico
                                                LEFT JOIN   gn_periodo p ON n.periodo = p.id_unico
                                                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico
                                                WHERE n.empleado = $idTer AND n.aplicabilidad = $apli AND n.periodo = 1
                                                ORDER BY  c.codigo ASC";
                                            $resultado = $mysqli->query($sql);
                                            $nreg = mysqli_num_rows($resultado);
                                    }
                                break;
                                case 4:
                                    if(!empty($idTer)){
                                        $sql = "SELECT      n.id_unico,
                                                            n.valor,
                                                            n.fecha,
                                                            n.empleado,
                                                            e.id_unico,
                                                            e.tercero,
                                                            t.id_unico,
                                                            CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                                                            n.periodo,
                                                            p.id_unico,
                                                            p.codigointerno,
                                                            n.concepto,
                                                            c.id_unico,
                                                            CONCAT(c.codigo,' - ',c.descripcion),
                                                            c.codigo
                                                            FROM gn_novedad n	 
                                                LEFT JOIN	gn_empleado e ON n.empleado = e.id_unico
                                                LEFT JOIN   gf_tercero t ON e.tercero = t.id_unico
                                                LEFT JOIN   gn_periodo p ON n.periodo = p.id_unico
                                                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico
                                                WHERE n.empleado = $idTer AND n.aplicabilidad = $apli  AND n.periodo = $pid OR n.empleado = $idTer AND n.aplicabilidad = 1 AND n.periodo = $pid OR n.empleado = $idTer AND n.aplicabilidad = 3 AND n.periodo = $pid OR n.empleado = $idTer AND n.aplicabilidad = 2 AND n.periodo = $pid
                                                ORDER BY  c.codigo ASC";
                                        $resultado = $mysqli->query($sql);
                                        $nreg = mysqli_num_rows($resultado);
                                    }
                                break;
                            }


                        ?>
                        <div class="col-sm-8 col-md-8 col-lg-8" style="margin-top: 5px;">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <td style="display: none;">Identificador</td>
                                            <td width="7%" class="cabeza"></td>
                                            <!-- Actualización 24 / 02 10:43 No es necesario mostrar el nombre del empleado
                                            <td class="cabeza"><strong>Empleado</strong></td>
                                            -->
                                            <td class="cabeza"><strong>Fecha</strong></td>
                                            <td class="cabeza"><strong>Concepto</strong></td>
                                            <td class="cabeza"><strong>Valor</strong></td>
                                            <td class="cabeza"><strong>Período</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="cabeza" style="display: none;">Identificador</th>
                                            <th class="cabeza" width="7%"></th>
                                            <!-- Actualización 24 / 02 10:43 No es necesario mostrar el nombre del empleado
                                            <th class="cabeza">Empleado</th>
                                            -->
                                            <th class="cabeza">Fecha</th>
                                            <th class="cabeza">Concepto</th>
                                            <th class="cabeza">Valor</th>
                                            <th class="cabeza">Período</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                            if($nreg>0){
                                                while ($row = mysqli_fetch_row($resultado)) {
                                                    $nfec = $row[2];
                                                    if(!empty($row[2])||$row[2]!=''){
                                                        $nfec = trim($nfec, '"');
                                                        $fecha_div = explode("-", $nfec);
                                                        $anion = $fecha_div[0];
                                                        $mesn = $fecha_div[1];
                                                        $dian = $fecha_div[2];
                                                        $nfec = $dian.'/'.$mesn.'/'.$anion;
                                                    }else{

                                                        $nfec = '';
                                                    }


                                                    $nid    = $row[0];
                                                    $nval   = $row[1];
                                                    #$nfec   = $row[2];
                                                    $nemp   = $row[3];
                                                    $empid  = $row[4];
                                                    $empter = $row[5];
                                                    $terid  = $row[6];
                                                    $ternom = $row[7];
                                                    $nper   = $row[8];
                                                    $pid    = $row[9];
                                                    $pci    = $row[10];
                                                    $ncon   = $row[11];
                                                    $conid  = $row[12];
                                                    $concn  = $row[13];
                                        ?>
                                                    <tr>
                                                        <td style="display: none;"><?php echo $row[0]?></td>
                                                    <td>
                                                        <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                            <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                        </a>
                                                        <a href="modificar_GN_NOVEDAD.php?id=<?php echo md5($row[0]);?>">
                                                            <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                                        </a>
                                                    </td>
                                                    <!-- Actualización 24 / 02 10:47 No es necesario mostrar el nombre del empleado
                                                    <td class="campos"><?php #echo $ternom?></td>
                                                    -->
                                                    <td class="campos"><?php echo $nfec?></td>
                                                    <td class="campos"><?php echo $concn?></td>
                                                    <td class="campos"><?php echo number_format($nval, 2, '.', ',');?></td>
                                                    <td class="campos"><?php echo $pci?></td>
                                        <?php   }
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


            </div>
        </div>
    </div>
    <div>
    <?php require_once './footer.php'; ?>
        <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Novedad?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal1" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información eliminada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" onclick="recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal3" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Debe seleccionar una opción de aplicabilidad.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal4" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Liquidación generada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" onclick="javaScript:recargar();" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <!--Modal para informar al usuario que no se ha podido registrar -->
  <div class="modal fade" id="myModal5" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido generar la liquidación.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<div class="modal fade" id="modal_cal_p" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar las novedades?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver_cal_p"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>

<div class="modal fade" id="modal_cal" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información eliminada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver_cal" onclick="recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>


<div class="modal fade" id="modal_cal_2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver_cal_2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>


  <!--Script que dan estilo al formulario-->
  <script src="js/bootstrap.min.js"></script>
<!--Scrip que envia los datos para la eliminación-->
<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarNovedadJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true)
                      $("#myModal1").modal('show');
                 else
                      $("#myModal2").modal('show');
                  }
              });
          });
      }
  </script>

<script type="text/javascript">
                    
    $("#liquidar").click(function()
    {
        <?php
            $per = $_GET['periodo'];
            $proceso = "SELECT tipoprocesonomina FROM gn_periodo WHERE id_unico = '$per'";
            $proc = $mysqli->query($proceso);
            $P = mysqli_fetch_row($proc); 
        ?>
        var pro = <?php echo $P[0]; ?>;
        var idemp = $("#sltEmpleado").val();
        var idper = $("#txtIdper").val();
        var fr = 1;
        var form_data = { id_emp: idemp, id_per: idper, fr : fr };
        var url = '';
        console.log(pro);
        if(pro == 1){
            url = "json/liquidarNominaJson2.php";
        }else if(pro == 2){
            url = "json/liquidarPrimaJson.php";
        }else if(pro == 5){
            url = "json/liquidarBonTerJson.php";
        }else if(pro == 6){
            url = "json/liquidarBonDirJson.php";
        }else if(pro == 7){
            url = "json/liquidarPrimaVacJson.php";
        }else if(pro == 8){
            var form_data = { sltEmpleado: idemp, sltPeriodo: idper, fr : fr, t:2 };
            url = "jsonNomina/gn_primaNavidad.php";
        }else if(pro == 12){
            var form_data = { sltEmpleado: idemp, sltPeriodo: idper, fr : fr };
            url = "jsonNomina/gn_LiquidacionRetroactivo.php";
        }else if(pro == 9){
            var form_data = { sltEmpleado: idemp, sltPeriodo: idper, fr : fr };
            url = "jsonNomina/gn_LiquidacionFinal.php";
        }else if(pro == 11){
            var form_data = { sltEmpleado: idemp, sltPeriodo: idper, fr : fr };
            url = "jsonNomina/gn_Liquidacion_Cesantias.php";
        }
            $.ajax({
                type: "GET",

                url: url,
                data: form_data,
                success: function(response)
                {
                    result = JSON.parse(response);
                    result = true;
                    if(result==true){
                        console.log('aa');
                        $("#myModal4").modal('show');
                        $("#ver1").click(function(){
                            console.log('aa');
                           //window.location.reload();
                        })
                    } else{
                        $("#myModal5").modal('show');
                        $("#ver1").click(function(){
                            console.log('aa');
                           window.location.reload();
                        })
                    }
                }
            });
        
        
                
    });
     
</script>
    <script src="js/md5.js"></script>
    <script type="text/javascript">
        $("#btnImprimir").click(function()
        {
            var idemp = $("#sltEmpleado").val();
            var idper = $("#txtIdper").val();
            <?php
                $per = $_GET['periodo'];
                $proceso = "SELECT tipoprocesonomina FROM gn_periodo WHERE id_unico = '$per'";
                $proc = $mysqli->query($proceso);
                 $P = mysqli_fetch_row($proc); 

            ?>
            var pro = <?php echo $P[0]; ?>;
            if(pro ==1 ){
                window.open('informes/generar_INF_VOLANTE_PAGO.php?sltEmpleado='+(idemp)+'&sltPeriodo='+(idper));
            }else if(pro == 2){
                window.open('informes/generar_INF_VOLANTE_PRIMA.php?id_emp='+md5(idemp)+'&id_per='+md5(idper));
            }else if(pro == 5){
                window.open('informes/generar_INF_VOLANTE_BONF_TER.php?id_emp='+md5(idemp)+'&id_per='+md5(idper));
            }else if(pro == 6){ 
                window.open('informes/generar_INF_VOLANTE_BON_DIR.php?id_emp='+md5(idemp)+'&id_per='+md5(idper));
            }else if(pro == 7){ 
                window.open('informes/generar_INF_VOLANTE_PRIMAVAC.php?sltEmpleado='+(idemp)+'&sltPeriodo='+(idper));
            }else if(pro == 8){ 
                window.open('informes_nomina/INF_VOLANTES.php?sltEmpleado='+(idemp)+'&sltPeriodo='+(idper)+'&sltTipo=8');
            }else if(pro == 9){ 
                window.open('informes_nomina/gn_liquidacion_final.php?e='+(idemp)+'&p='+(idper));
            }else if(pro == 11){ 
                window.open('informes_nomina/INF_VOLANTES.php?sltEmpleado='+(idemp)+'&sltPeriodo='+(idper)+'&sltTipo=11');
            }
            
        });
         function calavera()
          {
            $("#btnCalavera").click(function()
            {
                var idemp = $("#sltEmpleado").val();
                var idper = $("#txtIdper").val();
                $("#modal_cal_p").modal('show');
                $("#ver_cal_p").click(function(){
                      $("#modal_cal_p").modal('hide');
                      $.ajax({
                          type:"GET",
                          url:"json/eliminarNovedadJson.php?id=0&emp="+idemp+"&peri="+idper+"&opc=2",
                          success: function (data) {
                          result = JSON.parse(data);
                         // console.log(result);
                          if(result==true)
                              $("#modal_cal").modal('show');
                         else
                              $("#modal_cal_2").modal('show');
                          }
                      });
                  });
                
            });
        }
    </script>    

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
  <script type="text/javascript">    
      $('#ver2').click(function(){
        window.history.go(-1);
      });    
  </script>
</div>
  <script>
    function habilitar(){
        var liq= document.getElementById('liquidar').value;
        var fi = document.getElementById("liquidar");
        fi.style.visibility='visible';

        var Imp= document.getElementById('btnImprimir').value;
        var fl = document.getElementById("btnImprimir");
        fl.style.visibility='visible';
}
    function deshabilitar(){
        var liq= document.getElementById('liquidar').value;
        var fi = document.getElementById("liquidar");
        fi.style.visibility='hidden';

        var Imp= document.getElementById('btnImprimir').value;
        var fl = document.getElementById("btnImprimir");
        fl.style.visibility='hidden';
      
}

</script>
 <script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript"> 
         $("#sltConcepto").select2();
       
         $("#sltPeriodo").select2();
       
         $("#sltEmpleado").select2();

       
</script>

<!-- script que deshabilita el empleado cuando la aplicablidad es de tipo 1 o tipo 2 -->
<script type="text/javascript">

    <?php if($_GET['apli']=='1' || $_GET['apli']=='2') { ?> 
           
       $('#sltEmpleado').select2('disable');          

    <?php } ?>

</script>          
</body>
</html>