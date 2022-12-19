<?php
require_once ('Conexion/conexion.php');
require_once 'head_listar.php'; 
#PROCESO 
$id=$_GET['id'];
$query = "SELECT p.id_unico, "
        . "p.estado, "
        . "ep.nombre, "
        . "p.tipo_proceso, "
        . "tp.identificador, "
        . "tp.nombre, "
        . "p.tercero, "
        . "CONCAT(t.nombreuno, ' ', t.nombredos,' ', t.apellidouno,' ', t.apellidodos, '(',t.numeroidentificacion, ')') AS TERCERO, "
        . "p.proceso, "
        . "epp.nombre, "
        . "tpp.identificador, "
        . "tpp.nombre, "
        . "p.fecha, p.identificador "
        . "FROM gg_proceso p  "
        . "LEFT JOIN gg_estado_proceso ep ON p.estado = ep.id_unico "
        . "LEFT JOIN gg_tipo_proceso tp ON tp.id_unico = p.tipo_proceso "
        . "LEFT JOIN gf_tercero t ON p.tercero = t.id_unico "
        . "LEFT JOIN gg_proceso pr ON p.proceso = pr.id_unico "
        . "LEFT JOIN gg_estado_proceso epp ON pr.estado = epp.id_unico "
        . "LEFT JOIN gg_tipo_proceso tpp ON tpp.id_unico = pr.tipo_proceso "
        . "WHERE md5(p.id_unico)='$id'"; 
$procesos = $mysqli->query($query);
$ROPW=  mysqli_fetch_row($procesos);
$PROCESO= ucwords(strtolower($ROPW[13].'( '.$ROPW[4].' - '.$ROPW[5].' - '.$ROPW[2].')'));

#FORMA NOTIFICACIÓN
$notificacion = "SELECT id_unico, nombre FROM gg_forma_notificacion ORDER BY nombre ASC";
$notificacion = $mysqli->query($notificacion);

#BUSQUEDA DE FLUJO PROCESAL Y FASE A REGISTRAR
 #BUSQUEDA PARA SABER SI LA FASE 0 YA FUE REGISTRADA
$busqueda ="SELECT
            fp.id_unico
          FROM
            gg_detalle_proceso dp
          LEFT JOIN
            gg_proceso p ON dp.proceso = p.id_unico
          LEFT JOIN
            gg_flujo_procesal fp ON fp.id_unico = dp.flujo_procesal
          LEFT JOIN
            gg_fase f ON fp.fase = f.id_unico
          LEFT JOIN
            gg_elemento_flujo ef ON f.elemento_flujo = ef.id_unico
          WHERE f.id_unico = '0' AND dp.proceso ='$ROPW[0]'";     
$busqueda = $mysqli->query($busqueda);    
$num = mysqli_num_rows($busqueda);
#SI LA FASE NO HA SIDO REGISTRADA
if($num==0){ 
    #BUSQUEDA DEL FLUJO PROCESAL Y LA FASE 0,
    $fase = "SELECT
            fp.id_unico,
            fp.tipo_proceso,
            fp.tercero,
            fp.estado,
            fp.fase,
            f.nombre,
            f.elemento_flujo, 
            fp.duracion, fp.unidad_tiempo, fp.tipo_dia   
          FROM
            gg_flujo_procesal fp
          LEFT JOIN
            gg_fase f ON fp.fase = f.id_unico
          WHERE
            f.id_unico = '0' AND fp.tipo_proceso = '$ROPW[3]'";
    $fase= $mysqli->query($fase);
    $fase= mysqli_fetch_row($fase);
    $labelFase= $fase[5];
    $flujoProcesal=$fase[0];
    $estadof = $fase[3];
    $duracion = $fase[7];
    $unidadT = $fase[8];
    $tipoDia = $fase[9];
    $idultimo='';
    $fechaE='';
 } else { 
    #SI LA FASE YA FUE REGISTRADA
    #SE BUSCA EL ID DEL ULTIMO DETALLE QUE REGISTRADO
    $ultimoR ="SELECT MAX(id_unico) FROM gg_detalle_proceso WHERE proceso ='$ROPW[0]'";
    $ultimoR = $mysqli->query($ultimoR);
    $ultimoR = mysqli_fetch_row($ultimoR);
    $idultimo = $ultimoR[0];

    #SE BUSCA EL FLUJO PROCESAL REGISTRADO Y LA FASE RELACIONADA A ESE FLUJO, DEPENDIENDO DE LA ETAPA
    $flujoP = "SELECT
                dp.flujo_procesal,
                f.nombre,
                ef.nombre, 
                dp.condicion, 
                fp.duracion, 
                fp.unidad_tiempo, 
                fp.tipo_dia, 
                dp.fecha_ejecutada, 
                dp.estadoA 
              FROM
                gg_detalle_proceso dp
              LEFT JOIN
                gg_flujo_procesal fp ON dp.flujo_procesal = fp.id_unico
              LEFT JOIN
                gg_fase f ON fp.fase = f.id_unico
              LEFT JOIN
                gg_elemento_flujo ef ON f.elemento_flujo = ef.id_unico
              WHERE
                dp.id_unico ='$idultimo' AND fp.tipo_proceso = '$ROPW[3]'";
    $flujoP = $mysqli->query($flujoP);
    $flujoP = mysqli_fetch_row($flujoP);
    $flujoP1 = $flujoP[0];
    $duracion = $flujoP[4];
    $unidadT = $flujoP[5];
    $tipoDia = $flujoP[6];
    $fechaE= $flujoP[7];
    
    #DEPENDE DEL ELEMENTO FLUJO SE ESCOGE SI LA FASE ES SI O NO RELACIONADA AL FLUJO
    $condi= strtolower($flujoP[2]);
    if($condi=='condición'||$condi=='condicion'){?>
    <?php if($flujoP[3]=='2'){
             $flujo= "SELECT
                fp.flujo_no, 
                fp.fase,
                f.nombre, fp.id_unico, fpn.estado 
                FROM
                  gg_flujo_procesal fp
                LEFT JOIN
                  gg_flujo_procesal fpn ON fp.flujo_no = fpn.id_unico
                LEFT JOIN
                  gg_fase f ON fpn.fase = f.id_unico
                WHERE
                  fp.id_unico = '$flujoP1' AND fp.tipo_proceso = '$ROPW[3]'";

          $flujo = $mysqli->query($flujo);
          $flujo = mysqli_fetch_row($flujo);    
          $flujoProcesal = $flujo[0];
          $labelFase=$flujo[2];
          $estadof=$flujo[4];
          
    }else {
                $flujo= "SELECT
                fp.flujo_si, 
                fp.fase,
                f.nombre, fp.id_unico, fps.estado 
              FROM
                gg_flujo_procesal fp
              LEFT JOIN
                gg_flujo_procesal fps ON fp.flujo_si = fps.id_unico
              LEFT JOIN
                gg_fase f ON fps.fase = f.id_unico
              WHERE
                fp.id_unico = '$flujoP1' AND fp.tipo_proceso = '$ROPW[3]'";
            $flujo = $mysqli->query($flujo);
            $flujo = mysqli_fetch_row($flujo);
            $flujoProcesal = $flujo[0];
            $labelFase=$flujo[2];
            $estadof=$flujo[4];
        }
    }else {
        $flujo= "SELECT
            fp.flujo_si, 
            fp.fase,
            f.nombre, fp.id_unico, fps.estado 
          FROM
            gg_flujo_procesal fp
          LEFT JOIN
            gg_flujo_procesal fps ON fp.flujo_si = fps.id_unico
          LEFT JOIN
            gg_fase f ON fps.fase = f.id_unico
          WHERE
            fp.id_unico = '$flujoP1'";
    $flujo = $mysqli->query($flujo);
    $flujo = mysqli_fetch_row($flujo);
    $flujoProcesal = $flujo[0];
    $labelFase=$flujo[2];
    $estadof=$flujo[4];
    }

 }
$estadoP = strtolower($ROPW[1]);
$estadoF = strtolower($estadof);
 if(!empty($estadoF)){
     if($estadoP ==$estadoF){
         $estadog = $estadoP;
     }else {
         $estadog = $estadoF;
     }
 }else {
     $estadog = $estadoP;
 }
// echo $duracion;
// echo $unidadT;
// echo $tipoDia;
 
 #LISTAR
 $listar="SELECT
            dp.id_unico,
            f.nombre,
            dp.fecha_programada,
            dp.fecha_ejecutada,
            CONCAT(t.nombreuno,
              ' ',t.nombredos,
              ' ',t.apellidouno,
              ' ',t.apellidodos,
              '(',t.numeroidentificacion,')'
            ),
            fn.nombre,
            dp.observaciones, 
            ef.nombre, dp.tercero, dp.forma_notificacion 
          FROM
            gg_detalle_proceso dp
          LEFT JOIN
            gg_flujo_procesal fp ON dp.flujo_procesal = fp.id_unico
          LEFT JOIN
            gg_fase f ON fp.fase = f.id_unico
          LEFT JOIN
            gg_elemento_flujo ef ON f.elemento_flujo = ef.id_unico
          LEFT JOIN
            gf_tercero t ON dp.tercero = t.id_unico
          LEFT JOIN
            gg_forma_notificacion fn ON dp.forma_notificacion = fn.id_unico 
          WHERE md5(dp.proceso)='$id'";
 $listar = $mysqli->query($listar);
    ?>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
label#fecha_programada-error, #fecha_ejecutada-error, #formaN-error, #responsable-error, #condicion-error{
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
<script type="text/javascript">
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
            changeYear: true,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        var fechaProceso = '<?php echo date("d/m/Y", strtotime($ROPW[12]));?>';
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha_programada").datepicker({changeMonth: true, minDate: fechaProceso}).val();
        $("#fecha_ejecutada").datepicker({changeMonth: true, minDate: fechaProceso}).val();
        
        
    });
    </script>
 <?php $estado = strtolower($ROPW[2]);
 if ($estado =='anulado' || $estado =='cancelado' || $estado =='cerrado' || $fechaE =='NULL' || $fechaE =='0000-00-00') { 
     $labelFase= $estado;
     $flujoProcesal='';?>
    <script>
        $(function(){
        document.getElementById('fecha_programada').disabled=true;
        document.getElementById('fecha_ejecutada').disabled=true;
        document.getElementById('responsable1').disabled=true;
        document.getElementById('formaN1').disabled=true;
        document.getElementById('observaciones').disabled=true;
        document.getElementById('guardar').disabled=true;
        document.getElementById('etapae').disabled=true;
        document.getElementById('documentos').disabled=true;
        });
    </script>
 <?php } 
$condicion ="SELECT ef.nombre FROM gg_flujo_procesal fp "
         . "LEFT JOIN gg_fase f ON f.id_unico = fp.fase "
         . "LEFT JOIN gg_elemento_flujo ef ON f.elemento_flujo = ef.id_unico "
         . "WHERE fp.id_unico ='$flujoProcesal'";
 $condicion = $mysqli->query($condicion);
 $condicion = mysqli_fetch_row($condicion);
 $condicion = $condicion[0];
 $condicion =  strtolower($condicion);
 if($condicion =='condicion' || $condicion =='condición') { ?>
    <script>
        $(function(){
            
            document.getElementById('divCondicion').style.display = 'block';
            condicion = document.getElementById('condicion');
            condicion.setAttribute("required", "");
        });
    </script>
 <?php } ?>
 <?php #TERCERO   
$terceroFlujo="SELECT DISTINCT CONCAT(t.nombreuno, ' ', t.nombredos,' ', t.apellidouno,' ', t.apellidodos, '(',t.numeroidentificacion, ')') AS TERCERO, t.id_unico 
                FROM
                  gg_flujo_procesal fp
                LEFT JOIN
                  gf_tercero t ON fp.tercero = t.id_unico
                WHERE
                  fp.id_unico = '$flujoProcesal' ";
 $terceroFlujo= $mysqli->query($terceroFlujo);
 $numTercero = mysqli_fetch_row($terceroFlujo);
 if(!empty($numTercero[1]) || $numTercero[1]!=NULL){
        
     
     $id_tercero= $numTercero[1];
     $nombreTercero = $numTercero[0];
 } else {
     $id_tercero = $ROPW[6];
     $nombreTercero = $ROPW[7];
 }
 $responsable = "SELECT DISTINCT CONCAT(t.nombreuno,' ', t.nombredos,' ',t.apellidouno,' ',t.apellidodos) AS NOMBRE , "
                . "t.id_unico, t.numeroidentificacion "
                . "FROM gg_gestion_responsable gt "
                . "LEFT JOIN  gf_tercero t ON t.id_unico = gt.tercero_uno OR t.id_unico = gt.tercero_dos "
                . "WHERE t.id_unico != $id_tercero ORDER BY NOMBRE ASC";
 $responsable = $mysqli->query($responsable);
 ?>
    <style>
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px; width:300px}
table.dataTable tbody td,table.dataTable tbody td{padding:1px 10px;}

    .cabeza{
        white-space:nowrap;
        padding: 20px;
    }
    
    .campos{
        padding:-20px;
    }
    td{
        width: 300px;
        
    }
    .form-control {font-size: 12px;}
    
</style>

<?php #DURACION FECHA PROGRAMADA
$fechaEjecutadaA = "SELECT fecha_ejecutada FROM gg_detalle_proceso WHERE id_unico ='$idultimo'";
$fechaEjecutadaA =$mysqli->query($fechaEjecutadaA);
$fechaEjecutadaA= mysqli_fetch_row($fechaEjecutadaA);
$fechaEjecutadaA=$fechaEjecutadaA[0];
if(empty($fechaEjecutadaA)|| $fechaEjecutadaA=='0000-00-00') {
    $fechaEjecutadaA=$ROPW[12];
    $fechaEjecutada=$ROPW[12];
} else { 
$fecha = new DateTime($fechaEjecutadaA);
if(empty($duracion)|| $duracion =='0'){
    $fechaEjecutada =$fechaEjecutadaA; 
} else {
    if(empty($unidadT)|| $unidadT=='0' || $unidadT==NULL){
        $fechaEjecutada =$fechaEjecutadaA; 
    }else{
        $unidadT = "SELECT nombre FROM gg_unidad_tiempo WHERE id_unico = '$unidadT'";
        $unidadT =$mysqli->query($unidadT);
        $unidadT = mysqli_fetch_row($unidadT);
        $unidadT = strtolower($unidadT[0]);
        switch ($unidadT){
            case('dia'):
            case('dias'):
            case('días'):
            case('día'):
                if(!empty($tipoDia)  || $tipoDia!=NULL){
                    $tipoDia = "SELECT nombre FROM gg_tipo_dia WHERE id_unico = '$tipoDia'";
                    $tipoDia =$mysqli->query($tipoDia);
                    $tipoDia = mysqli_fetch_row($tipoDia);
                    $tipoDia = strtolower($tipoDia[0]);
                    switch ($tipoDia):
                        case('habiles'):
                        case('hábiles'):
                            $festivoss=0;
                            $sum=0;
                            $x=0;
                            while($x<=$duracion) {
                                $agregar = '"'.$x.' days'.'"';
                                $fecha = new DateTime($fechaEjecutadaA);
                                $fecha= date_add($fecha, date_interval_create_from_date_string($agregar));
                                $fechaCom= date_format($fecha, 'Y-m-d');
                                    $festivos = "SELECT fecha FROM gf_festivos";
                                    $festivos = $mysqli->query($festivos);
                                    while($rowfecha = mysqli_fetch_row($festivos)){
                                        if($rowfecha[0]==$fechaCom){
                                            $festivoss=$festivoss+1;
                                            $duracion =$duracion +1;
                                        }
                                    }
                               $x=$x+1;
                            }
                            $fechaEjecutada=$fechaCom;
                        break;
                        default :
                            $agregar = '"'.$duracion.' days'.'"';
                            $fecha= date_add($fecha, date_interval_create_from_date_string($agregar));
                            $fechaEjecutada= date_format($fecha, 'Y-m-d');
                        break;                    
                    endswitch;
                } else {
                    
                    $agregar = '"'.$duracion.' days'.'"';
                    $fecha= date_add($fecha, date_interval_create_from_date_string($agregar));
                    $fechaEjecutada= date_format($fecha, 'Y-m-d');
                }
                
            break;
            case('semana'):
            case('semanas'):
                $agregar = '"'.$duracion.' weeks'.'"';
                $fecha= date_add($fecha, date_interval_create_from_date_string($agregar));
                $fechaEjecutada= date_format($fecha, 'Y-m-d');
            break;
            case('mes'):
            case('meses'):
                $agregar = '"'.$duracion.' months'.'"';
                $fecha= date_add($fecha, date_interval_create_from_date_string($agregar));
                $fechaEjecutada= date_format($fecha, 'Y-m-d');
            break;
            case('año'):
            case('años'):
                $agregar = '"'.$duracion.' years'.'"';
                $fecha= date_add($fecha, date_interval_create_from_date_string($agregar));
                $fechaEjecutada= date_format($fecha, 'Y-m-d');
            break;
        
        }
    }
}
}
?>
<script>
    $(function(){
        var fechaProceso = '<?php echo date("d/m/Y", strtotime($ROPW[12]));?>';
        var fechadefault = '<?php echo date("d/m/Y", strtotime($fechaEjecutada));?>';
        var ejecutadaAnterior = '<?php echo date("d/m/Y", strtotime($fechaEjecutadaA));?>';
        $( "#fecha_programada" ).datepicker( "destroy" );
        $("#fecha_programada").datepicker({changeMonth: true, minDate: ejecutadaAnterior}).val(fechadefault);
        $( "#fecha_ejecutada" ).datepicker( "destroy" );
        $("#fecha_ejecutada").datepicker({changeMonth: true, minDate: ejecutadaAnterior}).val();
    });
</script>

<title>Detalle Proceso</title>
</head>
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-9 text-left" style="margin-left: -16px; ">
            <h2 align="center" class="tituloform" style="margin-top:-3px">Detalle Proceso</h2>
                <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none; margin-top: -5px" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-15px;  background-color: #0e315a; color: white; border-radius: 5px">PROCESO:<?php echo $PROCESO; ?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonProcesos/registrar_GG_DETALLE_PROCESOJson.php">
                <p align="center" style="margin-bottom: 20px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                <!--Flujo procesal, eestado, proceso-->
                <input type="hidden" id="flujoprocesal" name="flujoprocesal" value="<?php echo $flujoProcesal;?>">
                <input type="hidden" id="estado" name="estadog" value="<?php echo $estadog;?>">
                <input type="hidden" id="proceso" name="proceso" value="<?php echo $ROPW[0];?>">
                <input type="hidden" id="estadoAnterior" name="estadoAnterior" value="<?php echo $ROPW[1];?>">
                <div class="form-group form-inline" style="margin-left:0px; margin-top:-25px">
                    <!--Fase-->
                    <div class="form-group form-inline"style="margin-left:5px" >
                        <label style="width:100px; margin-left: 58px;margin-bottom: 10px; display:inline" for="fase" class="control-label" ><strong style="color:#03C1FB;">*</strong>Fase:&nbsp;</label>
                        <label style="width:186px; text-align: left; margin-top:-6px" class="control-label" style="display:inline"><i><?php echo ucwords(strtolower($labelFase))?></i></label>
                    </div>
                    <!--Fecha Programada-->
                    <div class="form-group form-inline"style="margin-left:10px" >
                        <label style="width:100px;" for="fecha_programada" class="control-label"><strong style="color:#03C1FB;">*</strong>Fecha Programada:</label>
                        <input type="text" name="fecha_programada" id="fecha_programada" readonly="true" style="width:200px; display: inline; margin-top:12px; margin-left: 5px;" class="form-control" required="required" title="Ingrese Fecha Programada" placeholder="Fecha Programada">
                    </div>
                    <div class="form-group form-inline"style="margin-left:30px" >
                        <label style="width:100px;" for="fecha_ejecutada" class="control-label">Fecha Ejecutada:</label>
                        <input type="text" name="fecha_ejecutada" id="fecha_ejecutada" readonly="true" style="width:200px; display: inline;margin-top:12px; margin-left: 5px;" class="form-control" title="Ingrese Fecha Ejecutada" placeholder="Fecha Ejecutada">
                    </div>
                </div>
                <div class="form-group form-inline" style="margin-left:0px; margin-top:-30px">
                    <!--RESPONSABLE-->
                    <div class="form-group form-inline"style="margin-left:8px" >
                        <label style="width:100px; margin-bottom: 10px;" for="responsable" class="control-label" ><strong style="color:#03C1FB;">*</strong>Responsable:&nbsp;</label>
                       <input type="hidden" name="responsable" required id="responsable" value="<?php echo $id_tercero;?>" title="Seleccione responsable">
                        <select style="width:180px;" name="responsable1" id="responsable1" required="required" class="select2_single form-control" title="Seleccione responsable" required="required" onchange="llenarR();">
                            <option value="<?php echo $id_tercero?>"><?php echo ucwords(strtolower($nombreTercero));?></option>
                                <?php  while($rowr = mysqli_fetch_row($responsable)){?>
                            <option value="<?php echo $rowr[1] ?>"><?php echo ucwords((strtolower($rowr[0].'('.$rowr[2].')')));}?></option>;
                        </select> 
                    </div>
                    <!--Forma Notificación-->
                    <div class="form-group form-inline" style="margin-left:13px">
                        <label style="width:100px;margin-right: 5px;" for="formaN" class="control-label"><strong style="color:#03C1FB;">*</strong>Forma Notificación:</label>
                        <?php while ($row3=  mysqli_fetch_row($notificacion)) { 
                                    $comp = strtolower($row3[1]); 
                                    if($comp=='na' || $comp =='n.a' || $comp=='n.a.') {
                                        $idforma=$row3[0];
                                        $nombreForma= $row3[1];
                                    } else{
                                        $idforma='';
                                        $nombreForma='-';
                                    }
                                 }
                                $forman= "SELECT id_unico, nombre FROM gg_forma_notificacion WHERE id_unico != '$idforma'";    
                                $forman=$mysqli->query($forman);?>
                            <input type="hidden" name="formaN" required id="formaN" value="<?php echo $idforma?>" title="Seleccione forma notificación">
                        <select style="width:200px; " name="formaN1" id="formaN1" required="required" class="select2_single form-control" title="Seleccione Forma Notificación" required="required" onchange="llenarF();">
                            <option value="<?php echo $idforma?>"><?php echo ucwords(strtolower($nombreForma));?></option>
                            <?php while($row2 = mysqli_fetch_row($forman)){?>
                            <option value="<?php echo $row2[0] ?>"><?php echo ucwords((strtolower($row2[1])));}?></option>;
                        </select> 
                    </div>
                    
                    <!--OBSERVACIONES-->
                    <div class="form-group form-inline"style="margin-left:33px" >
                        <label style="width:80px;" for="observaciones" class="control-label">Observaciones:</label>
                        <textarea type="text" name="observaciones" id="observaciones"  style=" display: inline; margin-left:22px;  width: 200px; height: 65px" class="form-control"  title="Ingrese Observaciones" placeholder="Observaciones" maxlength="500"></textarea>
                    </div>
                    
                    <div class="form-group form-inline" style="margin-left:10px">
                         
                        <button id="guardar" type="submit" class="btn btn-primary sombra" title="Guardar" style="margin-left:8px; margin-top: 15px"> <i class="glyphicon glyphicon-floppy-disk" ></i></button>
                    </div>
                    
                </div>
                <div align="center" id="divCondicion" style="display: none; margin-left:0px; margin-top:-35px" class="form-group form-inline" >
                    <!--CONDICION-->
                    <div class="form-group form-inline"style="margin-left:8px" >
                        <label for="condicion" class="control-label" ><strong style="color:#03C1FB;">*</strong><?php echo '¿'.$labelFase.'?:'?></label>
                    </div>
                    <div class="form-group form-inline"style="margin-left:20px; margin-top: 5px;" >
                        <input type="radio" name="condicion" id="condicion"  title="Escoja una opción "value="1">Sí
                        <input type="radio" name="condicion" id="condicion" title="Escoja una opción " value="2">No
                    </div>
                    
                </div>
                </form>
            </div>
             <input type="hidden" id="idPrevio" value="">
               <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed text-center" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="oculto"></td>
                                    <td style="min-width: 20px; max-width: 20px;" align="center"></td>
                                    <td style="min-width: 200px;max-width: 200px;"><strong>Fase</strong></td>
                                    <td style="min-width: 100px;max-width: 100px;"><strong>Fecha Programada</strong></td>
                                    <td style="min-width: 100px;max-width: 100px;"><strong>Fecha Ejecutada</strong></td>
                                    <td style="min-width: 200px;max-width: 200px;"><strong>Responsable</strong></td>
                                    <td style="min-width: 150px;max-width: 150px;"><strong>Forma Notificación</strong></td>
                                    <td style="min-width: 200px;max-width: 200px;"><strong>Observaciones</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto"></th>
                                    <th style="min-width: 20px; max-width: 20px;"></th>
                                    <th style="min-width: 200px;max-width: 200px;">Fase</th>
                                    <th style="min-width: 100px;max-width: 100px;">Fecha Programada</th>
                                    <th style="min-width: 100px;max-width: 100px;">Fecha Ejecutada</th>
                                    <th style="min-width: 200px;max-width: 200px;">Responsable</th>
                                    <th style="min-width: 150px;max-width: 150px;">Forma Notificación</th>
                                    <th style="min-width: 200px;max-width: 200px;">Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                <?php while($rowl = mysqli_fetch_row($listar)){ ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $rowl[0]?></td>
                                        <?php if ($idultimo==$rowl[0]) {?>
                                        <td style="min-width: 20px; max-width: 20px;" class="campos">
                                            <a href="#" onclick="eliminar(<?php echo $rowl[0];?>)">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <?php $ef = strtolower($rowl[7]);
                                            if($ef=='condicion' || $ef=='condición') { } else { ?>
                                            <a href="#" onclick="javascript:modificar(<?php echo $rowl[0]; ?>);return select(<?php echo $rowl[0]; ?>)">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                            <?php } ?>
                                        </td>
                                        <?php } else { ?>
                                        <td style="min-width: 20px; max-width: 20px;" class="campos"></td>
                                        <?php } ?>
                                        <td class="text-left" style="min-width: 200px;max-width: 200px;">
                                            <?php echo '<label style="text-align:left; font-weight:normal" id="labelFase'.$rowl[0].'">'.ucwords(strtolower($rowl[1])).'</label>'; ?>
                                        </td>
                                        <?php $idAultimo= "SELECT MAX(id_unico) FROM gg_detalle_proceso WHERE proceso ='$ROPW[0]' AND id_unico != '$rowl[0]'";
                                        $idAultimo=$mysqli->query($idAultimo);
                                        $idAultimo = mysqli_fetch_row($idAultimo);
                                        $idAultimo = $idAultimo[0];
                                        $fechaEjeAnterior = "SELECT fecha_ejecutada FROM gg_detalle_proceso WHERE id_unico = '$idAultimo'";
                                        $fechaEjeAnterior = $mysqli->query($fechaEjeAnterior);
                                        $fechaEjeAnterior = mysqli_fetch_row($fechaEjeAnterior);
                                        $fechaEjeAnterior = $fechaEjeAnterior[0];?>
                                        <script>
                                            var fechaProceso = '<?php echo date("d/m/Y", strtotime($ROPW[12]));?>';
                                            var fechaEjecAnterior = '<?php echo date("d/m/Y", strtotime($fechaEjeAnterior));?>';
                                            if(fechaEjecAnterior =='' ||  fechaEjecAnterior=='NULL'){
                                                var fechamin = fechaProceso;
                                            } else {
                                                var fechamin = fechaEjecAnterior;
                                            }
                                        </script>
                                        <td class="text-left" style="min-width: 100px;max-width: 100px;">
                                            <?php
                                            echo '<label style="text-align:left; font-weight:normal" id="labelFechaP'.$rowl[0].'">'. date("d/m/Y", strtotime($rowl[2])).'</label>'; ?>
                                            <input type="text" name="inputFechaP<?php echo $rowl[0]?>" readonly="true" id="inputFechaP<?php echo $rowl[0]?>" title="Ingrese la fecha programada" class="form-control" style="display:none; width: 100px">
                                            <script>
                                                var fechaP = '<?php echo date("d/m/Y", strtotime($rowl[2]));?>';
                                                $("#inputFechaP<?php echo $rowl[0]?>").datepicker({changeMonth: true, minDate: fechamin}).val(fechaP);
                                            </script>
                                        </td>
                                        <td class="text-left" style="min-width: 100px;max-width: 100px;">
                                            <?php if(empty($rowl[3])|| $rowl[3]=='0000-00-00') { echo '';} else {
                                            echo '<label style="text-align:left; font-weight:normal" id="labelFechaE'.$rowl[0].'">'. date("d/m/Y", strtotime($rowl[3])).'</label>'; }?>
                                            <input type="text" name="inputFechaE<?php echo $rowl[0]?>" readonly="true" id="inputFechaE<?php echo $rowl[0]?>" title="Ingrese la fecha ejecutada" class="form-control" style="display:none; width: 100px">
                                             <script>
                                                 <?php if(empty($rowl[3])|| $rowl[3]=='0000-00-00') { ?>
                                                     var fechaE = fechamin;
                                                 <?php } else {?>
                                                     var fechaE = '<?php echo date("d/m/Y", strtotime($rowl[3]));?>';
                                                 <?php } ?>
                                                $("#inputFechaE<?php echo $rowl[0]?>").datepicker({changeMonth: true, minDate: fechamin}).val(fechaE);
                                            </script>
                                            
                                        </td>
                                        <td class="text-left" style="min-width: 200px;max-width: 200px;">
                                            <?php echo '<label style="text-align:left; font-weight:normal" id="labelResponsable'.$rowl[0].'">'.ucwords(strtolower($rowl[4])).'</label>'; ?>
                                            <div id="divResponsable<?php echo $rowl[0]?>" name="divResponsable<?php echo $rowl[0]?>" style="display: none">
                                                <?php  $responsable = "SELECT DISTINCT CONCAT(t.nombreuno,' ', t.nombredos,' ',t.apellidouno,' ',t.apellidodos,'(',t.numeroidentificacion,')') AS NOMBRE , "
                                                                . "t.id_unico "
                                                                . "FROM gg_gestion_responsable gt "
                                                                . "LEFT JOIN  gf_tercero t ON t.id_unico = gt.tercero_uno OR t.id_unico = gt.tercero_dos "
                                                                . "WHERE t.id_unico != $rowl[8] ORDER BY NOMBRE ASC";
                                                        $responsable = $mysqli->query($responsable);?>
                                                 <select name="selectResponsable<?php echo $rowl[0]?>" id="selectResponsable<?php echo $rowl[0]?>" class="form-control" style="width: 180px">
                                                    <option value="<?php echo $rowl[8]?>"><?php echo ucwords(strtolower($rowl[4]))?></option>
                                                    <?php while($row1 = mysqli_fetch_row($responsable)){?>
                                                        <option value="<?php echo $row1[1] ?>"><?php echo ucwords((strtolower($row1[0])));}?></option>;
                                                    <?php ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="text-left" style="min-width: 150px;max-width: 150px;">
                                            <?php echo '<label style="text-align:left; font-weight:normal" id="labelFormaN'.$rowl[0].'">'.ucwords(strtolower($rowl[5])).'</label>'; ?>
                                            <div id="divForma<?php echo $rowl[0]?>" name="divForma<?php echo $rowl[0]?>" style="display: none">
                                                <?php  $notificacion = "SELECT id_unico, nombre FROM gg_forma_notificacion WHERE id_unico != $rowl[9] ORDER BY nombre ASC";
                                                        $notificacion = $mysqli->query($notificacion);?>
                                                <select name="selectFormaN<?php echo $rowl[0]?>" id="selectFormaN<?php echo $rowl[0]?>" class="form-control" style="width: 130px">
                                                    <option value="<?php echo $rowl[9]?>"><?php echo ucwords(strtolower($rowl[5]))?></option>
                                                    <?php while($row2 = mysqli_fetch_row($notificacion)){?>
                                                    <option value="<?php echo $row2[0] ?>"><?php echo ucwords((strtolower($row2[1])));}?></option>;
                                                </select>
                                            </div>
                                        </td>
                                        <td class="text-left" style="min-width: 200px;max-width: 200px;">
                                            <?php echo '<label style="text-align:left; font-weight:normal" id="labelObservaciones'.$rowl[0].'">'.ucwords(strtolower($rowl[6])).'</label>'; ?>
                                            <div id ="divFinal<?php echo $rowl[0]?>" name ="divFinal<?php echo $rowl[0]?>" style="display:none">
                                                <textarea name="textObservaciones<?php echo $rowl[0]?>" id="textObservaciones<?php echo $rowl[0]?>" class="form-control" style="width: 150px; height: 40px; display: inline"><?php echo $rowl[6]?></textarea>
                                            <table id="tab<?php echo $rowl[0] ?>" name="tab<?php echo $rowl[0] ?>" style="display:inline">
                                                <tbody>
                                                    <tr style="background-color:transparent;">
                                                        <td style="background-color:transparent; width:8px">
                                                            <a  href="#<?php echo $rowl[0];?>" title="Guardar" id="guardar<?php echo $rowl[0]; ?>"  onclick="javascript:guardarCambios(<?php echo $rowl[0]; ?>)">
                                                                <li class="glyphicon glyphicon-floppy-disk"></li>
                                                            </a>
                                                        </td>
                                                        <td style="background-color:transparent; width:8px">
                                                            <a href="#<?php echo $rowl[00];?>" title="Cancelar" id="cancelar<?php echo $rowl[0] ?>" onclick="javascript:cancelar(<?php echo $rowl[0];?>)" >
                                                                <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            </div>
                                        </td>
                                        
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
        <div class="col-sm-6 col-sm-1" style="margin-top:-24px; margin-left: -20px" >
            <table class="tablaC table-condensed" style="margin-left: -3px; ">
                <thead>
                    <th>
                        <h2 class="titulo" align="center" style=" font-size:17px; height:35px">Adicional</h2>
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="#" onclick="etapaEspecial();"><button id="etapae" class="btn btnInfo btn-primary">ETAPA ESPECIAL</button></a><br/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button id="documentos" class="btn btnInfo btn-primary">DOCUMENTOS</button><br/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--Modales eliminar-->
<div class="modal fade" id="myModalEliminar" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de detalle proceso?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="verE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
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
                <p>Información eliminada correctamente</p>
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
                <p>No se pudo eliminar la información, el registro seleccionado esta siendo usado por otra dependencia.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver4" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal25" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver25" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal26" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La información no se ha podido modificar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver26" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<?php require_once 'footer.php';?>
</body>
<script src="js/select/select2.full.js"></script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
  </script>
  <script>
      function llenarR(){
          var responsable = document.getElementById('responsable1').value;
          document.getElementById('responsable').value=responsable;
      }
      function llenarF(){
          var forma = document.getElementById('formaN1').value;
          document.getElementById('formaN').value=forma;
      }
  </script>
  <script>
    function eliminar(id){
        $("#myModalEliminar").modal('show');
        $("#verE").click(function(){
             $("#myModalEliminar").modal('hide');
             $.ajax({
                 type:"GET",
                 url:"jsonProcesos/eliminar_GG_DETALLE_PROCESOJson.php?id="+id,
                 success: function (data) {
                 result = JSON.parse(data);
                 if(result==true){
                     $("#myModal3").modal('show');
                     $("#ver3").click(function(){
                         document.location.reload(); 
                   });
                 }else{
                     $("#myModal4").modal('show');
                     $("#ver4").click(function(){
                       $("#myModal4").modal('hide');
                   });
                 }}
             });
         });
    }
</script>
<script>
function select(id){
            var responsable = 'selectResponsable'+id;
            $(".select2_single, #"+responsable).select2();
            var formaN = 'selectFormaN'+id;
            $(".select2_single, #"+formaN).select2();
        }
</script>
 <script>
      function modificar(id){
            if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
                    var inputFechaP = 'inputFechaP'+$("#idPrevio").val();
                    var labelFechaP = 'labelFechaP'+$("#idPrevio").val();
                    var inputFechaE = 'inputFechaE'+$("#idPrevio").val();
                    var labelFechaE= 'labelFechaE'+$("#idPrevio").val();
                    var divResponsable = 'divResponsable'+$("#idPrevio").val();
                    var labelResponsable= 'labelResponsable'+$("#idPrevio").val();
                    var divForma = 'divForma'+$("#idPrevio").val();
                    var labelFormaN= 'labelFormaN'+$("#idPrevio").val();
                    var divFinal = 'divFinal'+$("#idPrevio").val();
                    var labelObservaciones= 'labelObservaciones'+$("#idPrevio").val();
                   
                    
                    
                    $("#"+inputFechaP).css('display','none');                               
                    $("#"+labelFechaP).css('display','block');
                    $("#"+inputFechaE).css('display','none');
                    $("#"+labelFechaE).css('display','block');
                    $("#"+divResponsable).css('display','none');
                    $("#"+labelResponsable).css('display','block');
                    $("#"+divForma).css('display','none');
                    $("#"+labelFormaN).css('display','block');
                    $("#"+divFinal).css('display','none');
                    $("#"+labelObservaciones).css('display','block');
                    
                    
                }
                var inputFechaP = 'inputFechaP'+id;
                var labelFechaP = 'labelFechaP'+id;
                var inputFechaE = 'inputFechaE'+id;
                var labelFechaE= 'labelFechaE'+id;
                var divResponsable = 'divResponsable'+id;
                var labelResponsable= 'labelResponsable'+id;
                var divForma = 'divForma'+id;
                var labelFormaN= 'labelFormaN'+id;
                var divFinal = 'divFinal'+id;
                var labelObservaciones= 'labelObservaciones'+id;
              
                
                $("#"+inputFechaP).css('display','block');                               
                $("#"+labelFechaP).css('display','none');
                $("#"+inputFechaE).css('display','block');
                $("#"+labelFechaE).css('display','none');
                $("#"+divResponsable).css('display','block');
                $("#"+labelResponsable).css('display','none');
                $("#"+divForma).css('display','block');
                $("#"+labelFormaN).css('display','none');
                $("#"+divFinal).css('display','block');
                $("#"+labelObservaciones).css('display','none');
               
                if($("#idPrevio").val() != id){
                    $("#idPrevio").val(id);   
                }
      }
  </script>
  <script>
      function cancelar(id){
          
            
            var inputFechaP = 'inputFechaP'+id;
            var labelFechaP = 'labelFechaP'+id;
            var inputFechaE = 'inputFechaE'+id;
            var labelFechaE= 'labelFechaE'+id;
            var divResponsable = 'divResponsable'+id;
            var labelResponsable= 'labelResponsable'+id;
            var divForma = 'divForma'+id;
            var labelFormaN= 'labelFormaN'+id;
            var divFinal = 'divFinal'+id;
            var labelObservaciones= 'labelObservaciones'+id;

            $("#"+inputFechaP).css('display','none');                               
            $("#"+labelFechaP).css('display','block');
            $("#"+inputFechaE).css('display','none');
            $("#"+labelFechaE).css('display','block');
            $("#"+divResponsable).css('display','none');
            $("#"+labelResponsable).css('display','block');
            $("#"+divForma).css('display','none');
            $("#"+labelFormaN).css('display','block');
            $("#"+divFinal).css('display','none');
            $("#"+labelObservaciones).css('display','block');
      }
  </script>
   <script type="text/javascript">
            function guardarCambios(id){
                
                var fechaP = 'inputFechaP'+id;
                var fechaE = 'inputFechaE'+id;
                var responsable = 'selectResponsable'+id;
                var forma = 'selectFormaN'+id;
                var observaciones= 'textObservaciones'+id;
                var form_data = {
                    is_ajax:1,
                    id:+id,
                    fechaP:$("#"+fechaP).val(),
                    fechaE:$("#"+fechaE).val(),
                    responsable:$("#"+responsable).val(),
                    forma:$("#"+forma).val(),
                    observaciones:$("#"+observaciones).val()
                };
                var result='';
                $.ajax({
                    type: 'POST',
                    url: "jsonProcesos/modificar_GG_DETALLE_PROCESOJson.php",
                    data:form_data,
                    success: function (data) {
                        result = JSON.parse(data);                        
                        if (result==true) {
                            $("#myModal25").modal('show');
                            $('#ver25').click(function(){
                                $("#myModal25").modal('hide');
                                document.location.reload(); 
                            });
                        }else {  
                            $("#myModal26").modal('show'); 
                            $('#ver26').click(function(){
                                $("#myModal26").modal('hide');
                              document.location.reload(); 
                            });
                        }                                                                        
                    }
                });
            }
        </script>
    <div class="modal fade" id="myModalRegistrarEtapaEspecial" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content client-form1">
                <div id="forma-modal" class="modal-header">       
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Etapa Especial</h4>
                </div>
                <form  name="form" method="POST" action="javascript:registrarEtapaEspecial()">
                    <div class="modal-body ">
                        <div class="form-group" style="margin-top: 13px;">
                            <label class="text-right" style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Flujo Procesal:</label>
                            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="etapaE" id="etapaE" class="select2_single form-control" title="Seleccione Etapa Especial" required>
                                <option value="">Etapa Especial</option>

                            </select>                                
                        </div> 
                        <div class="form-group" style="margin-top: 13px;">
                            <label class="text-right" style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Fecha Programada:</label>
                            <input type="text" name="fecha_programada_ee" id="fecha_programada_ee" readonly="true" title="Fecha programada" required="requiered" style="width:250px; height:35px">
                        </div>
                        <div class="form-group" style="margin-top: 13px;">
                            <label class="text-right" style="display:inline-block; width:140px">Fecha Ejecutada:</label>
                            <input type="text" name="fecha_ejecutada_ee" id="fecha_ejecutada_ee" readonly="true" title="Fecha ejecutada" style="width:250px; height:32px">
                        </div>
                        <div class="form-group" style="margin-top: 13px;">
                            <label  class="text-right" style="display:inline-block; width:140px">Elemento Relacional:</label>
                            <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="etapaRelacionada" id="etapaRelacionada" class="select2_single form-control" title="Seleccione Elemento Flujo Relacionado">
                                <option value="">Elemento Relacional</option>

                            </select>                                
                        </div>
                    </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>

                    <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
                </div>
                </form>
            </div>
        </div>
    </div>
  <div class="modal fade" id="myModal11" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Información registrada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver11" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal12" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La información no se ha podido registrar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver12" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
   <script>
        function etapaEspecial(){
        tipo_proceso = <?php echo $ROPW[3]?>;
        var fechaProceso = '<?php echo date("d/m/Y", strtotime($ROPW[12]));?>';
        var fechadefault = '<?php echo date("d/m/Y", strtotime($fechaEjecutada));?>';
        var ejecutadaAnterior = '<?php echo date("d/m/Y", strtotime($fechaEjecutadaA));?>';
        $( "#fecha_programada_ee" ).datepicker( "destroy" );
        $("#fecha_programada_ee").datepicker({changeMonth: true, minDate: ejecutadaAnterior}).val(fechadefault);
        $( "#fecha_ejecutada_ee" ).datepicker( "destroy" );
        $("#fecha_ejecutada_ee").datepicker({changeMonth: true, minDate: ejecutadaAnterior}).val();
         var form_data={
            existente:14,
            tipo_proceso:tipo_proceso      
        };
        $.ajax({
            type: 'POST',
            url: "consultasBasicas/consultarNumeros.php",
            data:form_data,
            success: function (data) { 
                $("#etapaE").html(data).fadeIn();
                $("#etapaE").css('display','none');
                
                var form_data={
                    existente:15,
                    tipo_proceso:tipo_proceso
                };
                $.ajax({
                    type: 'POST',
                    url: "consultasBasicas/consultarNumeros.php",
                    data:form_data,
                    success: function (data) { 
                        $("#etapaRelacionada").html(data).fadeIn();
                        $("#etapaRelacionada").css('display','none');
                        $("#myModalRegistrarEtapaEspecial").modal('show');
                    }
                });
            }
        });
       }
   </script>
   <script>
        
       function registrarEtapaEspecial(){
          
            var proceso = <?php echo $ROPW[0]?>;
            var tercero = <?php echo $id_tercero ?>;
            var flujo= document.getElementById('etapaE').value;
            var fechaP= document.getElementById('fecha_programada_ee').value;
            var fechaE= document.getElementById('fecha_ejecutada_ee').value;
            var flujoR= document.getElementById('etapaRelacionada').value;
          
            var form_data={
              proceso:proceso,
              tercero: tercero,
              flujo:flujo,
              fechaP: fechaP,
              fechaE:fechaE,
              flujoR:flujoR
          };
          $.ajax({
                  type:"POST",
                  url:"jsonProcesos/registrar_GG_ETAPA_ESPECIALJson.php",
                  data:form_data,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true) {
                      
                      $("#myModal11").modal('show');
                      $('#ver11').click(function(){
                          $("#myModalRegistrarEtapaEspecial").modal('hide');
                        document.location.reload(); 
                      });
                  } else { 
                      $("#myModal12").modal('show');
                      $('#ver12').click(function(){
                           $("#myModalRegistrarEtapaEspecial").modal('hide');
                        document.location.reload(); 
                      });
                  }
                  }
              });
      }
      
  </script>
  <script>
  $("#etapaE").change(function() {
      var id = document.getElementById('etapaE').value;
      var tipo_proceso = <?php echo $ROPW[3]?>;
      var form_data={
                    existente:16,
                    id:id, 
                    tipo:tipo_proceso
                };
                $.ajax({
                    type: 'POST',
                    url: "consultasBasicas/consultarNumeros.php",
                    data:form_data,
                    success: function (data) { 
                        $("#etapaRelacionada").html(data).fadeIn();
                        $("#etapaRelacionada").css('display','none');
                    }
                });
  });
  </script>
