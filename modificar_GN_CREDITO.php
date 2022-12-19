<?php 
#14/06/2017 --- Nestor B --- se modificó la consulta, se corrigio la fecha y se agrego el campo de concepto
require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT    c.id_unico,
                    c.empleado,
                    e.id_unico,
                    e.tercero,
                    t.id_unico,
                    CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                    c.tipoproceso,
                    tpn.id_unico,
                    tpn.nombre,
                    c.entidad,
                    ter.id_unico,
                    ter.razonsocial,
                    c.numerocredito,
                    c.fecha,
                    c.periodoinicia,
                    c.valorcredito,
                    c.numerocuotas,
                    c.valorcuota,
                    p.id_unico,
                    p.codigointerno,
                    c.concepto,
                    CONCAT(co.codigo,' - ',co.descripcion)
                FROM gn_credito c	 
                LEFT JOIN	gn_empleado e               ON c.empleado = e.id_unico
                LEFT JOIN   gf_tercero t                ON e.tercero = t.id_unico
                LEFT JOIN   gn_tipo_proceso_nomina tpn  ON c.tipoproceso = tpn.id_unico
                LEFT JOIN   gf_tercero ter              ON c.entidad = ter.id_unico
                LEFT JOIN   gn_periodo p                ON c.periodoinicia = p.id_unico
                LEFT JOIN   gn_concepto co              ON c.concepto = co.id_unico
                where md5(c.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
    $cid   = $row[0];
    $cemp  = $row[1];
    $eid   = $row[2];
    $eter  = $row[3];
    $tid1  = $row[4];
    $ter1  = $row[5];
    $ctip  = $row[6];
    $tpid  = $row[7];
    $tpnom = $row[8];
    $cent  = $row[9];
    $tid2  = $row[10];
    $ter2  = $row[11];
    $cncr  = $row[12];
    $cfec  = $row[13];
    $cper  = $row[14];
    $cval  = $row[15];
    $cncu  = $row[16];
    $cvcu  = $row[17];
    $perr  = $row[19];
    $conc  = $row[20];
    $desco = $row[21];
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<style >
   table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
   table.dataTable tbody td,table.dataTable tbody td{padding:1px}
   .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
       font-family: Arial;}
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
               
        $("#Fecha").datepicker({changeMonth: true,}).val();
       
        
        
});
</script>
<title>Modificar Crédito</title>
<link href="css/select/select2.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Modificar Crédito</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-top:-5px; margin-left: -10px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarCreditoJson.php">
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
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico != $cemp";
                        $empleado = $mysqli->query($emp);
                        ?>  
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="select2_single form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required="">
                            <option value="<?php echo $cemp?>"><?php echo $ter1?></option>
                                <?php 
                                while ($filaEM = mysqli_fetch_row($empleado)) { ?>
                                <option value="<?php echo $filaEM[0];?>"><?php echo ucwords(($filaEM[3])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Empleado-->
<!------------------------- Consulta para llenar campo Entidad-->
                        <?php 
                        if(empty($cent))
                        $en = "SELECT          pt.perfil,
                                                pt.tercero,
                                                t.razonsocial,
                                                p.id_unico,
                                                p.nombre
                            FROM gf_perfil_tercero pt 
                            LEFT JOIN gf_tercero t  ON pt.tercero = t.id_unico
                            LEFT JOIN gf_perfil p ON pt.perfil = p.id_unico
                            WHERE pt.perfil = '12'";
                       else
                          $en = "SELECT          pt.perfil,
                                                pt.tercero,
                                                t.razonsocial,
                                                p.id_unico,
                                                p.nombre
                            FROM gf_perfil_tercero pt 
                            LEFT JOIN gf_tercero t  ON pt.tercero = t.id_unico
                            LEFT JOIN gf_perfil p ON pt.perfil = p.id_unico
                            WHERE pt.perfil = 12 AND t.id_unico != $cent";
                        $ent = $mysqli->query($en);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Entidad:
                            </label>
                            <select name="sltEntidad" class="select2_single form-control" id="sltEntidad" title="Seleccione entidad" style="height: 30px">
                            <option value="<?php echo $tid2?>"><?php echo $ter2?></option>
                                <?php 
                                while ($filaEN = mysqli_fetch_row($ent)) { ?>
                                <option value="<?php echo $filaEN[1];?>"><?php echo $filaEN[2]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""></option>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Entidad-->
<!------------------------- Consulta para llenar Tipo Proceso-->
                        <?php 
                      if(empty($ctip))
                        $tp = "SELECT id_unico, nombre FROM gn_tipo_proceso_nomina";
                      else
                        $tp = "SELECT id_unico, nombre FROM gn_tipo_proceso_nomina WHERE id_unico != $ctip";
                              
                        $tpro = $mysqli->query($tp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Tipo:
                            </label>
                            <select name="sltTipo" class="select2_single form-control" id="sltTipo" title="Seleccione Tipo" style="height: 30px">
                            <option value="<?php echo $tpid?>"><?php echo $tpnom?></option>
                                <?php 
                                while ($filaTP = mysqli_fetch_row($tpro)) { ?>
                                <option value="<?php echo $filaTP[0];?>"><?php echo $filaTP[1]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""></option>
                            </select>   
                        </div>
                        <?php
                            $concep = "SELECT id_unico, CONCAT(codigo,' - ',descripcion) FROM gn_concepto WHERE id_unico != $conc";
                            $conce = $mysqli->query($concep);
                            
                        ?>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="concepto"  class="col-sm-5 control-label"><strong class="obligado"></strong>Concepto:</label>
                                <select name="sltConcepto" id="sltConcepto" class="select2_single form-control" title="Seleccione Concepto" style="height: 30px">
                                    <option value="<?php echo $conc; ?>"><?php echo $desco; ?></option>
                                    <?php
                        
                                        while($c = mysqli_fetch_row($conce)){ ?>
                            
                                            <option value="<?php echo $c[0]; ?>"><?php  echo $c[1]; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                        </div>
<!----------Fin Consulta Para llenar Tipo Proceso-->                              
<!----------Campo para llenar Número Crédito-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="numerocr" class="col-sm-5 control-label"><strong class="obligado">*</strong>No. Crédito:</label>
                     <input required="required" type="text" name="txtNumeroC" id="txtNumeroC" class="form-control" value="<?php echo $cval?>" maxlength="100" title="Ingrese el número del crédito" onkeypress="return txtValida(event,'num')" placeholder="Número Crédito">
                </div>                                    
<!----------Fin Campo Número Crédito-->
<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
<!------------------------- Campo para seleccionar Fecha-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fecha" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha:</label>
                <?php
                    $cfec = $row[13];
                    if(!empty($row[13])||$row[13]){
                           $cfec = trim($cfec, '"');
                           $fecha_div = explode("-", $cfec);
                           $anioa = $fecha_div[0];
                           $mesa = $fecha_div[1];
                           $diaa = $fecha_div[2];
                           $cfec = $diaa.'/'.$mesa.'/'.$anioa;
                    }else{

                           $cfec = '';
                    } 
                ?>
                <input style="width:auto" class="col-sm-2 input-sm" type="text" name="Fecha" id="Fecha" step="1" value="<?php echo $cfec;?>">
           </div>
<!----------Fin Captura de Fecha-->
<!------------------------- Campo para seleccionar Período Inicial-->
            <?php
                $perio = "SELECT id_unico, codigointerno FROM gn_periodo WHERE id_unico != $row[18] AND liquidado !=1 AND id_unico !=1  ";
                $period = $mysqli->query($perio);
            ?>
    <input class="hidden" name="txtcoper" id="txtcoper" type="text" value="<?php $row[18]; ?>">
           <div class="form-group" style="margin-top: -10px;">
                <label for="periodo"  class="col-sm-5 control-label"><strong class="obligado"></strong>Período Inicial:</label>
                <select name="sltPeriodoI" id="sltPeriodoI" class="select2_single form-control" title="Seleccione Periodo" style="height: 30px">
                    <option value="<?php echo $row[18]; ?>"><?php echo $perr; ?></option>
                    <?php
                        
                        while($p = mysqli_fetch_row($period)){ ?>
                            
                    <option value="<?php echo $p[0]; ?>"><?php  echo $p[1]; ?></option>
                    <?php
                        }
                    ?>
                </select>
           </div>
<!----------Fin Captura de Período Inicial--> 
<!----------Campo para llenar Número de Cuotas-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="ncuotas" class="col-sm-5 control-label"><strong class="obligado"></strong>No. Cuotas:</label>
                     <input type="text" name="txtNCuotas" id="txtNcuotas" value="<?php echo $cncu;?>" class="form-control" maxlength="100" title="Ingrese el número de cuotas" onkeypress="return txtValida(event,'num')" placeholder="Número de cuotas">
                </div>                                    
<!----------Fin Campo para llenar Número de Cuotas-->
<!----------Campo para llenar valor credito-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="valorcr" class="col-sm-5 control-label"><strong class="obligado"></strong>Valor:</label>
                     <input type="text" name="txtValorCr" id="txtValorCr" value="<?php echo $cval;?>" class="form-control" maxlength="100" title="Ingrese el valor del crédito" onkeypress="return txtValida(event,'num')" placeholder="Valor Crédito">
                </div>                                    
<!----------Fin Campo para llenar valor credito-->
<!----------Campo para llenar Valor Cuota-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="valorcu" class="col-sm-5 control-label"><strong class="obligado"></strong>Valor Cuota:</label>
                     <input type="text" name="txtValorCu" id="txtValorCu" value="<?php echo $cvcu;?>" class="form-control" maxlength="100" title="Ingrese el valor de la cuota" onkeypress="return txtValida(event,'num')" placeholder="Valor Cuota">
                </div>                                    
<!----------Fin Campo para llenar Valor Cuota-->                                         
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
        <script src="js/select/select2.full.js"></script>
        <script>
         $(document).ready(function() {
         $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
    </script>
    </body>
</html>
