<?php
###################################################################################################
#
#04/04/2017 creado por KAREN 

####################################################################################################

require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
#session_start();
$vig = $_SESSION['anno'];
@$id = $_GET['idE'];
@$fec = $_GET['fec'];
@$id_p = $_GET['idP'];



$emp = "SELECT e.id_unico, e.tercero, CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno,t.apellidodos ) , t.tipoidentificacion, ti.id_unico, CONCAT(ti.nombre,' ',t.numeroidentificacion)
FROM gn_empleado e
LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
WHERE md5(e.id_unico) = '$id'";
$bus = $mysqli->query($emp);
$busq = mysqli_fetch_row($bus);
$idT = $busq[0];
$datosTercero= $busq[2].' ('.$busq[5].')';
$a = "none";
$a2 = "none";
if(empty($id_T))
{
    $tercero = "Empleado";    
}
else
{
    $tercero = $datosTercero;
    
}
if(empty($id_p))
{
     
}
else
{
    
    $a="inline-block";
}
//'3'
@$empl_sim=$_GET['sltemp'];
//'30/11/218'
@$fecha_retiro=$_GET['fec'];
//"inline-block"
@$a2=$_GET['op'];
if(empty($a2))
{
     $a2="none";
}
else
{
    
    $a2="inline-block";
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
    label #sltEmpleado-error, #sltPeriodo-error, #fechaR-error {
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
        $(document).ready(function() {
            var i= 0;
            $('#tableO thead th').each( function () {
                if(i => 0) {
                    var title = $(this).text();
                    switch (i){
                        case 0:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 1:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 2:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 3:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 4:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 5:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                    }
                    i = i+1;
                } else {
                    i = i+1;
                }
            });
            
            var i2= 0;
            $('#tableO2 thead th').each( function () {
                if(i2 => 0) {
                    var title = $(this).text();
                    switch (i2){
                        case 0:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 1:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 2:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 3:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 4:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                        case 5:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                            break;
                    }
                    i2 = i2+1;
                } else {
                    i2 = i2+1;
                }
            });
            
            
            
            // DataTable
            var table = $('#tableO').DataTable({
                "autoFill": true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                },
                scrollY: 120,
                "scrollX": true,
                scrollCollapse: true,
                paging: false,
                fixedColumns:   {
                    leftColumns: 1
                },
                'columnDefs': [{
                    'targets': 0,
                    'searchable':false,
                    'orderable':false,
                    'className': 'dt-body-center'
                }]
            });
            
            // DataTable
            var table2 = $('#tableO2').DataTable({
                "autoFill": true,
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                },
                scrollY: 120,
                "scrollX": true,
                scrollCollapse: true,
                paging: false,
                fixedColumns:   {
                    leftColumns: 1
                },
                'columnDefs': [{
                    'targets': 0,
                    'searchable':false,
                    'orderable':false,
                    'className': 'dt-body-center'
                }]
            });
            
            
            
            var i = 0;
            table.columns().every( function () {
                var that = this;
                if(i!=0) {
                    $( 'input', this.header() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                    i = i+1;
                } else {
                    i = i+1;
                }
            });
            
            var i2 = 0;
            table2.columns().every( function () {
                var that = this;
                if(i2!=0) {
                    $( 'input', this.header() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                    i2 = i2+1;
                } else {
                    i2 = i2+1;
                }
            });
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
               
        $("#fechaR").datepicker({changeMonth: true,}).val();
        $("#sltFechaI").datepicker({changeMonth: true,}).val();
        $("#sltFechaF").datepicker({changeMonth: true,}).val();
        $("#sltFechaID").datepicker({changeMonth: true,}).val();
        $("#sltFechaFD").datepicker({changeMonth: true,}).val();
        
        
});
</script>
   <title>Liquidación Final</title>
    <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-10 text-left" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Liquidación Final</h2>
                    <!--<?php //echo 'listar_GN_VACACIONES.php?id='.$_GET['idE'];?>-->
                    
                    
                    <div class="client-form contenedorForma" style="margin-top: -6px;font-size: 13px">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_Liquidacion_Final_GN.php">
                              
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
<!--------------------------------------------------------------------------------------------------------------------- -->
                            <div class="form-group form-inline" style="margin-top:-25px; height: 100px;">
                                <?php 
                                    if(empty($idT))
                                    {
                                        $emp = "SELECT                         
                                                        e.id_unico,
                                                        e.tercero,
                                                        t.id_unico,
                                                        CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno,t.apellidodos ) 
                                        FROM gn_empleado e
                                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico where t.id_unico!=2";
                                        $idTer = "";
                                    }
                                    else
                                    {
                                        $emp = "SELECT                      
                                                        e.id_unico,
                                                        e.tercero,
                                                        t.id_unico,
                                                        CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno,t.apellidodos ) 
                                        FROM gn_empleado e
                                        LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico = 0";
                                        $idTer = $idT;
                                    }
                                    $empleado = $mysqli->query($emp);
                                ?>
                                <label for="sltEmpleado" class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Empleado:
                                </label>
                                <select required="required" name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 210px;height: 30px" class="form-control col-sm-1">
                                    <option value="<?php echo $idTer?>"><?php echo $tercero?></option>
                                        <?php 
                                            while($rowE = mysqli_fetch_row($empleado))
                                            {
                                                echo "<option value=".$rowE[0].">".$rowE[3]."</option>";
                                            }
                                        ?>                                                          
                                </select>

                            <!--Fecha-->
                        <label for="fechaR" class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Fecha Retiro:
                                </label>
                            
                        <input class="form-control col-sm-1" value="" type="text" required="required" name="fechaR" id="fechaR" title="Seleccione fecha" placeholder="Fecha Retiro"  style="width: 210px;" >
                            


                          <!--------------------------------------------------------------------- -->
                        <?php
                            $per = "SELECT  p.id_unico, CONCAT(p.codigointerno,' - ',tpn.nombre) FROM gn_periodo p LEFT JOIN gn_tipo_proceso_nomina tpn ON p.tipoprocesonomina = tpn.id_unico  WHERE tpn.id_unico = 9 AND p.liquidado !=1 AND p.parametrizacionanno = '$vig'";

                            $periodo = $mysqli->query($per);
                        ?>

                            
                        <label for="sltPeriodo"  class="control-label col-sm-1 col-md-1 col-lg-1"><strong class="obligado">*</strong>Periodo:</label>
                        <select required="required" name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="width: 150px;height: 30px" class="form-control col-sm-1">
                            <option value="">Periodo</option>
                            <?php 
                                while($rowE = mysqli_fetch_row($periodo))
                                {
                                    echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                }
                            ?>       
                        </select>
                                                        
                                                      
                          <!-- <label for="txtdiasT" class="col-sm-2 control-label">
                                <strong class="obligado"></strong>Días Trabajados:
                            </label>
                            <input  name="txtdiasT" id="txtdiasT" title="Ingrese Número de días trabajados" type="number" style="width: 140px;height: 30px" class="form-control col-sm-1" placeholder="Días trabajados"> -->


                                <label for="No" class="col-sm-10 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top: -3px; width:100px; margin-bottom: -15px;margin-left: 10px ;">Liquidar</button>  

                            </div>
                        </form> 

                        
                        <button type="cargar" class="btn btn-primary sombra col-sm-9" style="margin-top: -73px; width:150px; margin-bottom: -10px;margin-left: 720px;" onclick="myButton_onclick()">Calcular Liquidación</button>   
                        <script>
                            function myButton_onclick() {

                                var empl =$('#sltEmpleado').val();
                                var fec =$('#fechaR').val();
                                if(empl==='' || fec===''){
                                     $("#myModal_error").modal('show');
                                    
                                }else{
                                    var op =$('#inline-block').val();
                                    window.location='liquidar_GN_LIQUIDACION_FINAL.php?sltemp='+empl+'&fec='+fec+'&op='+op+'&idE='+empl;
                                }
                            }
                        </script>
                          <div class="col-sm-12 control-label"></div>

                        <div class="" style="margin-top:-5px; display:<?php echo $a2 ?>" >
                                <?php 
                                
                                    $sql_vin = "SELECT  e.id_unico, 
                                         e.tercero, 
                                         CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno,t.apellidodos )  as tercero, 
                                            c.salarioactual,
                                         (SELECT ingreso.fecha FROM gn_empleado e_ing 
                                          LEFT JOIN gn_vinculacion_retiro ingreso on ingreso.empleado=e_ing.id_unico 
                                          WHERE e_ing.id_unico = e.id_unico and ingreso.estado=1 order by ingreso.fecha desc LIMIT 1 )
                                          fecha_ingreso,
                                          (SELECT ingreso.id_unico FROM gn_empleado e_ing 
                                           LEFT JOIN gn_vinculacion_retiro ingreso on ingreso.empleado=e_ing.id_unico  
                                           WHERE e_ing.id_unico = e.id_unico and ingreso.estado=1 order by ingreso.fecha desc LIMIT 1 )
                                           id_ingreso, 
                                            
                                           (SELECT auxt FROM gn_parametros_liquidacion WHERE vigencia = $vig) as vlor_auxilio,
                                           (SELECT salmin FROM gn_parametros_liquidacion WHERE vigencia = $vig) as vlor_auxilio
                                            

                                    FROM gn_empleado e 
                                    LEFT JOIN gf_tercero t on e.tercero = t.id_unico
                                    LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                                    LEFT JOIN gn_categoria c ON c.id_unico = tc.categoria
                                    LEFT JOIN gn_categoria_riesgos cr ON e.tipo_riesgo = cr.id_unico
                                    WHERE e.id_unico = $empl_sim ";
                                    $res = $mysqli->query($sql_vin);
                                    $row_vin = mysqli_fetch_row($res);

                                    $id_tercero=$row_vin[1];
                                    $nom_tercero=$row_vin[2];
                                    $salario_tercero=$row_vin[3];
                                    $fecha_ingreso=$row_vin[4];
                                    $id_ingreso=$row_vin[5];
                                    $aux_trans=$row_vin[6];
                                    $vlr_salario_min=$row_vin[7];

                                    $sql_vin_ret = "SELECT retiro.* FROM gn_empleado e_retiro 
                                                    LEFT JOIN gn_vinculacion_retiro retiro on retiro.empleado=e_retiro.id_unico              
                                                    WHERE e_retiro.id_unico = $empl_sim and retiro.estado=2 and retiro.vinculacionretiro=$id_ingreso
                                                    order by retiro.fecha desc LIMIT 1 ";
                                        $res_ret = $mysqli->query($sql_vin_ret);
                                        $n_ret = mysqli_num_rows($res_ret);
                                        if($n_ret > 0){
                                        
                                          $row_ret = mysqli_fetch_row($res_ret);
                                          $id_retiro=$row_ret[0];
                                          $fecha_ret=$row_ret[3];
                                          $fcha_ret = explode('-', $fecha_ret);
                                          $a_ret = $fcha_ret[0];
                                          $m_ret = $fcha_ret[1];
                                          $d_ret = $fcha_ret[2];
                                          $fc_retiro = $a_ret.'-'.$m_ret.'-'.$d_ret;        
                                        }else{
                                          $id_retiro=0;
                                          $fecha_ret=$fecha_retiro;
                                          $fcha_ret = explode('/', $fecha_ret);
                                          $a_ret = $fcha_ret[2];
                                          $m_ret = $fcha_ret[1];
                                          $d_ret = $fcha_ret[0];
                                          $fc_retiro = $a_ret.'-'.$m_ret.'-'.$d_ret;        
                                        }

                                    $dias_trab = (strtotime($fecha_ingreso)-strtotime($fc_retiro))/86400;
                                    $dias_trab = abs($dias_trab); 
                                    $dias_trab = floor($dias_trab);
                                    $dias_prima=$dias_trab;
                                    $dias_vac=$dias_trab;
                                    $dias_ces=$dias_trab;
                                    
                                   $sql_dias_pr = "SELECT p.fechafin  FROM gn_novedad n left join gn_periodo p on p.id_unico= n.periodo where empleado=$empl_sim and p.tipoprocesonomina=2 and n.concepto=7 and p.fechainicio>='$fecha_ingreso' order by p.fechainicio desc";
                                    $res_pr = $mysqli->query($sql_dias_pr);
                                    $n_pr = mysqli_num_rows($res_pr);
                                    if($n_pr > 0)
                                    {
                                         $row_pr = mysqli_fetch_row($res_pr);
                                         $ffin_prima = explode('-', $row_pr[0]);
                                         if($ffin_prima[1]==12){
                                            $ms_inipr=1;
                                            $fini_prim= $a_ret.'-01-01';
                                         }else if($ffin_prima[1]==06){
                                            $ms_inipr=7;
                                            $fini_prim= $a_ret.'-07-01';
                                         }
                                         $cont=0;
                                         for($x=$ms_inipr;$x<=($m_ret-1);$x++){
                                            $cont++;
                                         }
                                         
                                         $dias_prima=($cont*30)+$d_ret;
                                    }
                                    //2. Consultar los dias pagados de vacaciones 

                                    $sql_dias_vac = "SELECT fechafin from gn_vacaciones where fechaInicio>='$fecha_ingreso' and empleado = $empl_sim order by fechafin desc ";
                                    $res_v = $mysqli->query($sql_dias_vac);
                                    $n_vac = mysqli_num_rows($res_v);
                                    if($n_vac > 0)
                                    {
                                         $row_v = mysqli_fetch_row($res_v);
                                         $fult_vac = $row_v[0];                                         
                                    }else{
                                         $fult_vac = $fecha_ingreso;
                                    }
                                    if($fult_vac==$fecha_ingreso){
                                        $dias_vac= $dias_vac;
                                    }else{
                                         $dias_vac = (strtotime($fult_vac)-strtotime($fc_retiro))/86400;
                                         $dias_vac = abs($dias_vac); 
                                         $dias_vac = floor($dias_vac);
                                    }
                                    $dias_vac = ($dias_vac * 15)/360;
                                    $dias_vac = round($dias_vac);
                                    /*
                                    $sql_dias_vac = "SELECT sum(n.valor) vlr FROM gn_novedad n
                                                  left join gn_periodo p on p.id_unico= n.periodo
                                                  where empleado=$empl_sim and p.tipoprocesonomina=7 and n.concepto=7  and n.fecha>='$fecha_ingreso'";
                                    $res_vac = $mysqli->query($sql_dias_vac);
                                    $n_vac = mysqli_num_rows($res_vac);
                                    if($n_vac > 0)
                                    {
                                         $row_vac = mysqli_fetch_row($res_vac);
                                         $dias_vac= $dias_vac-$row_vac[0];
                                    }*/

                                    //3. Consultar los dias pagados de vacaciones 
                                    $sql_dias_ces = "SELECT p.fechafin  FROM gn_novedad n left join gn_periodo p on p.id_unico= n.periodo where empleado=$empl_sim and p.tipoprocesonomina=11 and n.concepto=7 and p.fechainicio>='$fecha_ingreso' order by p.fechainicio desc";
                                    $res_c = $mysqli->query($sql_dias_ces);
                                    $n_c = mysqli_num_rows($res_c);
                                    if($n_c > 0)
                                    {
                                         $row_c = mysqli_fetch_row($res_c);
                                         $ffin_ces = explode('-', $row_c[0]);
                                         if($ffin_ces[1]==12){
                                            $fini_ces= ($ffin_ces[0]+1).'-01-01';
                                         }else {
                                            $fini_ces= $ffin_ces[0].'-01-01';
                                         }
                                         $dias_ces = (strtotime($fini_ces)-strtotime($fc_retiro))/86400;
                                         $dias_ces = abs($dias_ces); 
                                         $dias_ces = floor($dias_ces);
                                         if($dias_ces>360){
                                            $dias_ces = 360;
                                         }                                         
                                    }else{
                                        $dias_ces= $dias_ces;
                                    }
                                    /*
                                    $sql_dias_ces = "SELECT sum(n.valor) vlr FROM gn_novedad n
                                                  left join gn_periodo p on p.id_unico= n.periodo
                                                  where empleado=$empl_sim and p.tipoprocesonomina=11 and n.concepto=7 and n.fecha>='$fecha_ingreso' ";
                                    $res_ces = $mysqli->query($sql_dias_ces);
                                    $n_ces = mysqli_num_rows($res_ces);
                                    if($n_ces > 0)
                                    {
                                         $row_ces = mysqli_fetch_row($res_ces);
                                         $dias_ces= $dias_ces-$row_ces[0];
                                    }
                                    */
                                    //averiguar que otros conceptos entran como base de liquidacon para prima
                                      //PRIMA DE SERVICIOS
                                      $sql_vlhe_pr="SELECT cb.* FROM gn_concepto_base cb
                                                    LEFT JOIN gn_concepto c on c.id_unico=cb.id_concepto
                                                    where c.clase=1 and cb.id_concepto_aplica=439";
                                      $res_hepr = $mysqli->query($sql_vlhe_pr);    
                                      $n_he = mysqli_num_rows($res_hepr);
                                      $vl_promedio_pr=0;
                                      $vl_actual_pr=0;

                                        if($n_he > 0){
                                          while($row_he = mysqli_fetch_row($res_hepr)){
                                              $id_tipo_b=$row_he[3];
                                              $id_concepto=$row_he[1];
                                              if($id_tipo_b==1){
                                                //si el tipo base es actual buscamos el ultimo pago de ese concepto despues de la fecha de ingreso
                                                $sql_vl_act_con="SELECT * from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empl_sim order by n.fecha desc LIMIT 1 ";
                                                 $res_vlact = $mysqli->query($sql_vl_act_con);   
                                                 $n_vl = mysqli_num_rows($res_vlact);
                                                 if($n_vl > 0)
                                                 {
                                                    $row_act = mysqli_fetch_row($res_vlact);
                                                    $vl_actual_pr= $vl_actual_pr+$row_act[1];
                                                 }

                                              }else if($id_tipo_b==2){
                                                //si el tipo de base es promedio sumamos todos los valores pagados 
                                                $sql_vl_act_con="SELECT sum(n.valor) vlr from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empl_sim  ";
                                                 $res_vlact = $mysqli->query($sql_vl_act_con);   
                                                 $n_vl = mysqli_num_rows($res_vlact);
                                                 if($n_vl > 0)
                                                 {
                                                    $row_act = mysqli_fetch_row($res_vlact);
                                                    $vl_promedio_pr= $vl_promedio_pr+$row_act[0];
                                                 }
                                              }
                                          }
                                        }
                                        $vl_promedio_pr= ($vl_promedio_pr/$dias_prima)*30;
                                        $vl_actual_pr;

                                        //VACACIONES
                                      $sql_vlhe_vac="SELECT cb.* FROM gn_concepto_base cb
                                                    LEFT JOIN gn_concepto c on c.id_unico=cb.id_concepto
                                                    where c.clase=1 and cb.id_concepto_aplica=440";
                                      $res_hevac = $mysqli->query($sql_vlhe_vac);    
                                      $n_vac = mysqli_num_rows($res_hevac);
                                      $vl_promedio_vac=0;
                                      $vl_actual_vac=0;

                                        if($n_vac > 0){
                                          while($row_he = mysqli_fetch_row($res_hevac)){
                                              $id_tipo_b=$row_he[3];
                                              $id_concepto=$row_he[1];
                                              if($id_tipo_b==1){
                                                //si el tipo base es actual buscamos el ultimo pago de ese concepto despues de la fecha de ingreso
                                                $sql_vl_act_con="SELECT * from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empl_sim order by n.fecha desc LIMIT 1 ";
                                                 $res_vlact = $mysqli->query($sql_vl_act_con);   
                                                 $n_vl = mysqli_num_rows($res_vlact);
                                                 if($n_vl > 0)
                                                 {
                                                    $row_act = mysqli_fetch_row($res_vlact);
                                                    $vl_actual_vac= $vl_actual_vac+$row_act[1];
                                                 }

                                              }else if($id_tipo_b==2){
                                                //si el tipo de base es promedio sumamos todos los valores pagados 
                                                $sql_vl_act_con="SELECT sum(n.valor) vlr from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empl_sim  ";
                                                 $res_vlact = $mysqli->query($sql_vl_act_con);   
                                                 $n_vl = mysqli_num_rows($res_vlact);
                                                 if($n_vl > 0)
                                                 {
                                                    $row_act = mysqli_fetch_row($res_vlact);
                                                    $vl_promedio_vac= $vl_promedio_vac+$row_act[0];
                                                 }
                                              }
                                          }
                                        }
                                        $vl_promedio_vac= ($vl_promedio_vac/$dias_vac)*30;
                                        $vl_actual_vac;

                                        //cesantias
                                      $sql_vlhe_ces="SELECT cb.* FROM gn_concepto_base cb
                                                    LEFT JOIN gn_concepto c on c.id_unico=cb.id_concepto
                                                    where c.clase=1 and cb.id_concepto_aplica=441";
                                      $res_hec = $mysqli->query($sql_vlhe_ces);    
                                      $n_he = mysqli_num_rows($res_hec);
                                      $vl_promedio_ces=0;
                                      $vl_actual_ces=0;

                                        if($n_he > 0){
                                          while($row_he = mysqli_fetch_row($res_hec)){
                                              $id_tipo_b=$row_he[3];
                                              $id_concepto=$row_he[1];
                                              if($id_tipo_b==1){
                                                //si el tipo base es actual buscamos el ultimo pago de ese concepto despues de la fecha de ingreso
                                                $sql_vl_act_con="SELECT * from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empl_sim order by n.fecha desc LIMIT 1 ";
                                                 $res_vlact = $mysqli->query($sql_vl_act_con);   
                                                 $n_vl = mysqli_num_rows($res_vlact);
                                                 if($n_vl > 0)
                                                 {
                                                    $row_act = mysqli_fetch_row($res_vlact);
                                                    $vl_actual_ces= $vl_actual_ces+$row_act[1];
                                                 }

                                              }else if($id_tipo_b==2){
                                                //si el tipo de base es promedio sumamos todos los valores pagados 
                                                $sql_vl_act_con="SELECT sum(n.valor) vlr from gn_novedad n where n.concepto=$id_concepto and n.fecha>='$fecha_ingreso' and n.empleado=$empl_sim  ";
                                                 $res_vlact = $mysqli->query($sql_vl_act_con);   
                                                 $n_vl = mysqli_num_rows($res_vlact);
                                                 if($n_vl > 0)
                                                 {
                                                    $row_act = mysqli_fetch_row($res_vlact);
                                                    $vl_promedio_ces= $vl_promedio_ces+$row_act[0];
                                                 }
                                              }
                                          }
                                        }
                                        $vl_promedio_ces= ($vl_promedio_ces/$dias_ces)*30;
                                        $vl_actual_ces;



                                    $restriccion= $vlr_salario_min*2;
                                    if($salario_tercero<=$restriccion){
                                         $salario_base= $salario_tercero+$aux_trans;
                                    }else{
                                         $salario_base= $salario_tercero;
                                    }
                                      $salario_base_pr=$salario_base+$vl_promedio_pr+$vl_actual_pr;
                                      $salario_base_vac=$salario_tercero+$vl_promedio_vac+$vl_actual_vac;
                                      $salario_base_ces=$salario_base+$vl_promedio_ces+$vl_actual_ces;
                                      
                                      $vlr_prima=($salario_base_pr*$dias_prima)/360;
                                      $vlr_vac_ret=($salario_base_vac*$dias_trab)/720;
                                      $vlr_cesantias_pg=($salario_base_ces*$dias_ces)/360;
                                      $vlr_int_cesantias=($vlr_cesantias_pg*0.12*$dias_ces)/360;
                                      
                                      $vlr_prima =  round($vlr_prima, 2, PHP_ROUND_HALF_UP);
                                      $vlr_vac_ret =  round($vlr_vac_ret, 2, PHP_ROUND_HALF_UP);
                                      $vlr_cesantias_pg =  round($vlr_cesantias_pg, 2, PHP_ROUND_HALF_UP);
                                      $vlr_int_cesantias =  round($vlr_int_cesantias, 2, PHP_ROUND_HALF_UP);

                                    $total=$vlr_prima+$vlr_vac_ret+$vlr_cesantias_pg+$vlr_int_cesantias;

                                ?>
                                <!--Fecha Ingreso -->
                                <label for="Fecha_Ingreso" class="col-sm-2 control-label">
                                    
                                </label>
                                <label for="Fecha_Ingreso" class="col-sm-1 control-label">
                                    <strong class="obligado">*</strong>Fecha Ingreso:
                                </label>
                                <input class="form-control col-sm-1" type="text" name="fechaIng" id="fechaIng" title="Fecha Ingreso" placeholder="Fecha Ingreso"  style="width: 90px;" value="<?php echo $fecha_ingreso; ?>" readonly>
                                
                                    <!--Fecha-->
                                <label for="fechaR" class="col-sm-1 control-label">
                                            <strong class="obligado">*</strong>Fecha Retiro:
                                        </label>
                                    
                                <input class="form-control col-sm-1" type="text" name="fechaRet" id="fechaRet" title="Fecha Retiro" placeholder="Fecha Retiro"  style="width: 90px;" value="<?php echo $fc_retiro; ?>" readonly>
                                    
                                <!--Fecha-->
                                <label for="fechaR" class="col-sm-1 control-label">
                                            <strong class="obligado">*</strong>Salario Actual:
                                        </label>
                                    
                                <input class="form-control col-sm-1 text-right" type="text" name="salario" id="salario" title="Salario" placeholder="Salario"  style="width: 150px;" value="<?php echo number_format($salario_tercero,2,'.',','); ?>" readonly>

                                <!--Fecha-->
                                <label for="fechaR" class="col-sm-1 control-label">
                                            <strong class="obligado">*</strong>Auxilio Transporte:
                                        </label>
                                    
                                <input class="form-control col-sm-1 text-right" type="text" name="auxilio" id="auxilio" title="Auxilio Transporte" placeholder="Auxilio"  style="width: 100px;" value="<?php echo number_format($aux_trans,2,'.',','); ?>" readonly>

                                
                            </div>
                            <div class="table-responsive col-sm-11 " style="margin-left: 10px; margin-right: 20px;margin-top:10px; display:<?php echo $a2 ?>">
                                     <?php 
                                        require_once './menu.php'; 
                                        $tab_fd= '';
                                            $sql_t = "SELECT distinct c.id_unico,c.codigo,
                                                            concat (c.descripcion,' (', c.codigo,')')as n_con FROM gn_concepto c  where c.aplica_liquidacion_final=1
                                                            order by c.codigo asc";
                                
                                        $resultado_T = $mysqli->query($sql_t);
                                    ?>
                                    <div class="table-responsive " style="margin-left: 5px; margin-right: 5px; ">
                                        <table id="tabla" class="table table-striped table-condensed display" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <td style="display: none;">Identificador</td>
                                                    <td width="7%" class="cabeza"></td>
                                                    <td class="cabeza text-center"><strong>CONCEPTO</strong></td>
                                                    <td class="cabeza text-center"><strong>DIAS TRABAJADOS</strong></td>
                                                    <td class="cabeza text-center"><strong>BASE LIQUIDACIÓN POR CONCEPTO</strong></td>
                                                    <td class="cabeza text-center"><strong>VALOR</strong></td>
                                                    
                                                </tr>
                                                <tr>
                                                    <th style="display: none;">Identificador</th>
                                                    <th width="7%" class="cabeza"></th>
                                                    <th class="cabeza text-center"><strong>CONCEPTO</strong></th>
                                                    <th class="cabeza text-center"><strong>DIAS TRABAJADOS</strong></th>
                                                    <th class="cabeza text-center"><strong>BASE LIQUIDACIÓN POR CONCEPTO</strong></th>
                                                    <th class="cabeza text-center"><strong>VALOR</strong></th>
                                                    
                                                </tr>
                                            </thead>    
                                            <tbody>
                                                <?php 
                                                    
                                                    while ($row = mysqli_fetch_row($resultado_T)) {                                         
                                                            $id_con   = $row[0];
                                                            $n_con = $row[2];
                                                            $cod_con = $row[1];
                                                            
                                                        ?>
                                                        <tr>
                                                    <td style="display: none;"><?php echo $row[0]?></td>
                                                    <td></td>
                                                    <td class="campos text-left"><?php echo $n_con;?></td>
                                                    <?php 
                                                    if($cod_con=='C12'){
                                                        ?>
                                                        <td class="campos text-center"><?php echo number_format($dias_prima, 0, '.', ',');?></td> 
                                                        <td class="campos text-center"><?php echo number_format($salario_base_pr, 2, '.', ',');?></td> 
                                                        <td class="campos text-right"><?php echo number_format($vlr_prima, 2, '.', ',');?></td>   
                                                    <?php
                                                    }
                                                    else if($cod_con=='C13'){
                                                    ?>
                                                        <td class="campos text-center"><?php echo number_format($dias_vac, 0, '.', ',');?></td> 
                                                        <td class="campos text-center"><?php echo number_format($salario_base_vac, 2, '.', ',');?></td> 
                                                        <td class="campos text-right"><?php echo number_format($vlr_vac_ret, 2, '.', ',');?></td>   
                                                    <?php
                                                    }
                                                    else if($cod_con=='C14'){
                                                        ?>
                                                        <td class="campos text-center"><?php echo number_format($dias_ces, 0, '.', ',');?></td> 
                                                        <td class="campos text-center"><?php echo number_format($salario_base_ces, 2, '.', ',');?></td> 
                                                        <td class="campos text-right"><?php echo number_format($vlr_cesantias_pg, 2, '.', ',');?></td>  
                                                        <?php
                                                    }
                                                    else if($cod_con=='C15'){
                                                    ?>
                                                        <td class="campos text-center"><?php echo number_format($dias_ces, 0, '.', ',');?></td> 
                                                        <td class="campos text-center"><?php echo number_format($vlr_cesantias_pg, 2, '.', ',');?></td> 
                                                        <td class="campos text-right"><?php echo number_format($vlr_int_cesantias, 2, '.', ',');?></td> 
                                                    <?php
                                                    }
                                                    ?>
                                                       
                                                </tr> 
                                                <?php }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                         <label for="fechaR" class="col-sm-10 control-label text-right" style="margin-top: 5px;">
                                            <strong class="obligado"></strong>TOTAL:
                                        </label>
                                         <label for="fechaR" class="col-sm-2 control-label text-right" style="margin-top: 5px;">
                                            <strong class="obligado"></strong><?php echo number_format($total,2,'.',','); ?>
                                        </label>
                                        
                                    </div>


                        <div class="table-responsive" style="margin-left: 10px; margin-right: 20px;margin-top:10px; display:<?php echo $a ?>">
                                     <?php 
                                        require_once './menu.php'; 
                                        $tab_fd= '';
                                            $sql_t = "SELECT distinct e.id_unico,
                                                CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno,t.apellidodos )  as tercero
                                                FROM gn_novedad n 
                                                left join gn_empleado e on e.id_unico=n.empleado
                                                left join gf_tercero t on t.id_unico=e.tercero
                                                left join gn_concepto c on c.id_unico=n.concepto
                                                where  n.periodo=$id_p order by n.empleado asc";
                                
                                        $resultado_T = $mysqli->query($sql_t);
                                    ?>
                                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                                        <table id="tableO" class="table table-striped table-condensed display" cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <td style="display: none;">Identificador</td>
                                                    <td width="7%" class="cabeza"></td>
                                                    <td class="cabeza"><strong>EMPLEADO</strong></td>
                                                    <?php 
                                                    $sql = "SELECT distinct
                                                            concat (c.descripcion,' (', c.codigo,')')as n_con FROM gn_novedad n 
                                                            left join gn_empleado e on e.id_unico=n.empleado
                                                            left join gf_tercero t on t.id_unico=e.tercero
                                                            left join gn_concepto c on c.id_unico=n.concepto
                                                            where  n.periodo=$id_p order by c.codigo asc";
                                                    
                                                     $resultado = $mysqli->query($sql);
                                                     while ($row = mysqli_fetch_row($resultado)) {    
                                                          echo "<td class='cabeza'><strong>$row[0]</strong></td>";
                                                     }
                                                    ?>
                                                    <td class="cabeza"><strong>Total</strong></td>
                                                    
                                                </tr>
                                                
                                            </thead>    
                                            <tbody>
                                                <?php 
                                                    
                                                    while ($row = mysqli_fetch_row($resultado_T)) {                                         
                                                            $id_emp   = $row[0];
                                                            $n_tercero = $row[1];
                                                            
                                                            
                                                        ?>
                                                        <tr>
                                                    <td style="display: none;"><?php echo $row[0]?></td>
                                                    <td></td>
                                                    <td class="campos text-left"><?php echo $n_tercero;?></td>
                                                          
                                                <?php
                                                         $sql = "SELECT distinct n.id_unico,
                                                                    n.valor,c.codigo
                                                                           FROM gn_novedad n 
                                                                     left join gn_empleado e on e.id_unico=n.empleado
                                                                     left join gf_tercero t on t.id_unico=e.tercero
                                                                     left join gn_concepto c on c.id_unico=n.concepto
                                                                     where  n.periodo=$id_p and n.empleado=$id_emp order by c.codigo asc";

                                                        
                                                         $result = $mysqli->query($sql);
                                                        $tota=0;
                                                        while ($rowV = mysqli_fetch_row($result)) {  
                                                            $codigo_n=$rowV[2];
                                                            $vcuota=$rowV[1];
                                                            if($codigo_n=='1002' || $codigo_n=='1003' || $codigo_n=='1004'){

                                                            }else{
                                                                $tota=$tota+$vcuota;    
                                                            }
                                                            
                                                            ?>
                                                            <td class="campos text-center"><?php echo number_format($vcuota, 2, '.', ',');?></td>  
                                                            <?php
                                                            
                                                            
                                                        }
                                                        ?>
                                                        
                                                    <td class="campos text-right"><?php echo number_format($tota, 2, '.', ',');?></td>  
                                                    
                                                 
                                                </tr> 
                                                <?php }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
            </div>



                    </div>
                </div>
                
<!--------------------------------------------------------------------------------------------------->                        

           
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
          <p>¿Desea eliminar el registro seleccionado de Vacaciones?</p>
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
  <div class="modal fade" id="myModal_error" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Debe seleccionar los campos necesarios para el calculo.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver3" onclick="recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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


  <!--Script que dan estilo al formulario-->

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
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
                  url:"json/eliminarVacacionesJson.php?id="+id,
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
