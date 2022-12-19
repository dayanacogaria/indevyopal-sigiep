<?php 
#13/03/2017 --- Nestor B --- se modificó la variable que se eavlúa en el select de tipo de novedad
#05/04/2017 --- Nestor B --- se agregó la funcion del datepicker para modificar la vista de los calendarios
require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
  $id = (($_GET["id"]));
  
  $sql = "SELECT        i.id_unico,
                        i.numeroinc,
                        i.fechainicio,
                        i.numerodias,
                        i.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        i.fechaaprobacion,
                        i.numeroaprobacion,
                        i.estado,
                        ei.id_unico,
                        ei.nombre,
                        i.diagnostico,
                        i.tiponovedad,
                        tn.id_unico,
                        tn.nombre,
                        c.id_unico,
                        CONCAT(c.codigo,' - ',c.descripcion)
                FROM gn_incapacidad i
                LEFT JOIN	gn_empleado e             ON i.empleado    = e.id_unico
                LEFT JOIN   gf_tercero t              ON e.tercero     = t.id_unico
                LEFT JOIN   gn_estado_incapacidad ei  ON i.estado      = ei.id_unico
                LEFT JOIN   gn_tipo_novedad tn        ON i.tiponovedad = tn.id_unico
                LEFT JOIN   gn_concepto c             ON i.concepto = c.id_unico
                where md5(i.id_unico) = '$id'";

  $resultado = $mysqli->query($sql);
  $row = mysqli_fetch_row($resultado);    
    
        $inid   = $row[0];
        $inni   = $row[1];
        $infi   = $row[2];
        $innd   = $row[3];
        $inemp  = $row[4];
        $empid  = $row[5];
        $empter = $row[6];
        $terid  = $row[7];
        $ternom = $row[8];
        $infa   = $row[9];
        $inna   = $row[10];
        $inest  = $row[11];
        $eiid   = $row[12];
        $einom  = $row[13];
        $indiag = $row[14];
        $intn   = $row[15];
        $tnid   = $row[16];
        $tnnom  = $row[17];
        $idconc = $row[18]; 
        $concep = $row[19];

/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<style>
    label #sltPeriodo-error {
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
       
        
        $("#sltFechaI").datepicker({changeMonth: true,}).val();
        $("#sltFechaA").datepicker({changeMonth: true,}).val();
        
        
});
</script>
<title>Modificar Incapacidad</title>
<link href="css/select/select2.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Incapacidad / Licencia</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarIncapacidadJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
<!------------------------- Consulta para llenar campo Empleado-->
                        <?php 
                        $emp = "SELECT 						
                                                        e.id_unico,
                                                        e.tercero,
							                            t.id_unico,
                                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                            WHERE e.id_unico != $inemp";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="select2_single form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required="">
                            <option value="<?php echo $empid?>"><?php echo $ternom?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($empleado)) { ?>
                                    <option value="<?php echo $filaT[0];?>"><?php echo ucwords(($filaT[3])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Empleado-->
<!------------------------- Consulta para llenar campo Tipo Novedad-->
                        <?php 
          
                            $tip = "SELECT id_unico, nombre FROM gn_tipo_novedad WHERE id_unico != $intn";
                            $tipon = $mysqli->query($tip);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                <strong class="obligado"></strong>Tipo Novedad:
                            </label>
                            <select name="sltTipo" class="select2_single form-control" id="sltTipo" title="Seleccione tipo Novedad" style="height: 30px" >
                                <option value="<?php echo $tnid?>"><?php echo $tnnom?></option>
                                <?php 
                                    while ($filaTN = mysqli_fetch_row($tipon)) { ?>                   
                                        <option value="<?php echo $filaTN[0];?>"><?php echo $filaTN[1];?></option>
                    
                                <?php
                                    }
                                ?>
                            </select>   
                        </div>
<!------------------------- Fin Consulta para llenar Tipo Novedad-->
<!------------------------- Consulta para llenar campo Estado Incapacidad-->
                        <?php 
                            $es = "SELECT id_unico, nombre FROM gn_estado_incapacidad WHERE id_unico != $inest";
                            $est = $mysqli->query($es);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                <strong class="obligado"></strong>Estado:
                            </label>
                            <select name="sltEstado" class="select2_single form-control" id="sltEstado" title="Seleccione estado" style="height: 30px">
                                <option value="<?php echo $eiid?>"><?php echo $einom?></option>
                                <?php 
                                    while ($filaE = mysqli_fetch_row($est)) { ?>                   
                                        <option value="<?php echo $filaE[0];?>"><?php echo $filaE[1];?></option>
                                <?php
                                    }
                                ?>
                            </select>   
                        </div>
                        
                        <?php 
                            $co = "SELECT id_unico, CONCAT(codigo,' - ',descripcion) FROM gn_concepto WHERE id_unico != $idconc";
                            $conc = $mysqli->query($co);
                        ?>
                        <input class="hidden" type="text" name="txtCon" id="txtFechaI" value="<?php echo $row[18]; ?>">
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                <strong class="obligado"></strong>Concepto:
                            </label>
                            <select name="sltConcepto" class="select2_single form-control" id="sltConcepto" title="Seleccione Concepto" style="height: 30px">
                                <option value="<?php echo $idconc; ?>"><?php echo $concep; ?></option>
                                <?php 
                                    while ($filaC = mysqli_fetch_row($conc)) { ?>                   
                                        <option value="<?php echo $filaC[0];?>"><?php echo $filaC[1];?></option>
                                <?php
                                    }
                                ?>
                            </select>   
                        </div>
<!------------------------- Fin Consulta para llenar Estado Incapacidad-->
<!------------------------- Campo Llenar Número Incapacidad-->
                            <div class="form-group" style="margin-top: -10px;">
                                 <label for="numeroI" class="col-sm-5 control-label"><strong class="obligado"></strong>Número Incapacidad / Licencia:</label>
                                <input type="text" name="txtNumeroI" id="txtNumeroI" class="form-control" maxlength="100" title="Ingrese el número de incapacidad" onkeypress="return txtValida(event,'num')" placeholder="Número Incapacidad" value="<?php echo $inni?>">
                            </div>                              
<!------------------------- Fin Campo Llenar Número Incapacidad-->
<!------------------------- Campo Llenar Número Aprobación-->
                            <div class="form-group" style="margin-top: -10px;">
                                 <label for="numeroA" class="col-sm-5 control-label"><strong class="obligado"></strong>Número Aprobación:</label>
                                <input type="text" name="txtNumeroA" id="txtNumeroA" class="form-control" maxlength="100" title="Ingrese el número de aprobación" onkeypress="return txtValida(event,'num')" placeholder="Número Aprobación" value="<?php echo $inna?>">
                              </div>                              
<!------------------------- Fin Campo Llenar Número Incapacidad-->
<!------------------------- Campo Llenar Número Días-->
                            <div class="form-group" style="margin-top: -10px;">
                                 <label for="numeroD" class="col-sm-5 control-label"><strong class="obligado"></strong>Número Días:</label>
                                <input type="text" name="txtNumeroD" id="txtNumeroD" class="form-control" maxlength="100" title="Ingrese el número de días" onkeypress="return txtValida(event,'num')" placeholder="Número Días" value="<?php echo $innd?>">
                            </div>                              
<!------------------------- Fin Campo Llenar Número Días-->
<!------------------------- Campo Llenar Diagnóstico-->
                            <div class="form-group" style="margin-top: -10px;">
                                 <label for="diagnostico" class="col-sm-5 control-label"><strong class="obligado"></strong>Diagnóstico / Motivo:</label>
                                <input type="text" name="txtDiagnostico" id="txtDiagnostico" class="form-control" maxlength="100" title="Ingrese el diagnóstico" onkeypress="return txtValida(event,'car')" placeholder="Diagnóstico" value="<?php echo $indiag?>">
                            </div>                              
<!------------------------- Fin Campo Llenar Diagnóstico-->
<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
<!------------------------- Campo para seleccionar Fecha Inicio-->
<input class="hidden" type="text" name="txtFechaI" id="txtFechaI" value="<?php echo $row[2]; ?>">
           <div class="form-group" style="margin-top: -10px;">
                <label for="sltFechaI" type = "text" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Inicial:</label>
                <?php
                $infi      = $row[2];
                if(!empty($row[2])||$row[2]!=''){
                    $infi      = trim($infi, '"');
                    $fecha_div = explode("-", $infi);
                    $anioi     = $fecha_div[0];
                    $mesi      = $fecha_div[1];
                    $diai      = $fecha_div[2];
                    $infi      = $diai.'/'.$mesi.'/'.$anioi;
                }else{
                    $infi = '';
                }
                ?>
                <input style="width:auto"  type="text"  name="sltFechaI" id="sltFechaI" class="form-control col-sm-1" value="<?php echo $infi?>">
           </div>
<!----------Fin Captura de Fecha Inicio-->                                                            
<!------------------------- Campo para seleccionar Fecha Aprobación-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="sltFechaA" type = "text" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Aprobación:</label>
                <?php
                $infa      = $row[9];
                if(!empty($row[9])|| $row[9]!=''){
                    $infa      = trim($infa, '"');
                    $fecha_div = explode("-", $infa);
                    $aniofa    = $fecha_div[0];
                    $mesfa     = $fecha_div[1];
                    $diafa     = $fecha_div[2];
                    $infa      = $diafa.'/'.$mesfa.'/'.$aniofa;
                }else{

                    $infa = '';
                }
                ?>
                <input style="width:auto" class="col-sm-2 input-sm" type="text" name="sltFechaA" id="sltFechaA"  step="1" value="<?php echo $infa?>">
           </div>
<!----------Fin Captura de Fecha Aprobación-->                              
                                                           
                            <div class="form-group" style="margin-top: 10px;">
                              <label for="no" class="col-sm-5 control-label"></label>
                              <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                            </div>


                          </form>
                      </div>
                  </div>                  
              </div>
        </div>
        <?php require_once './footer.php'; ?>
         <script type="text/javascript" src="js/menu.js"></script>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script src="js/select/select2.full.js"></script>

        <script type="text/javascript"> 
            $("#sltEmpleado").select2();
       
            $("#sltConcepto").select2();
       
            $("#sltEstado").select2();
         
            $("#sltTipo").select2();
       
        </script>
    </body>
</html>
