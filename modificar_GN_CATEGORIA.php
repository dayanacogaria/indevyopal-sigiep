<?php require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT    c.id_unico,
                    c.codigointerno,
                    c.nombre,
                    c.salarioactual,
                    c.salarioanterior,
                    c.gastorepresentacion,
                    c.nivel,
                    n.id_unico,
                    n.nombre,
                    c.estadocategoria,
                    ec.id_unico,
                    ec.nombre,
                    c.parametrizacion_anno,
                    c.fecha_modificacion,
                    c.tipo_persona_sui 
                FROM gn_categoria c	 
                LEFT JOIN	gn_nivel n              ON c.nivel = n.id_unico
                LEFT JOIN   gn_estado_categoria ec  ON c.estadocategoria = ec.id_unico
                where md5(c.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
    $cid    = $row[0];
    $ccod   = $row[1];
    $cnom   = $row[2];
    $csalac = $row[3];
    $csalan = $row[4];
    $cgas   = $row[5];
    $cniv   = $row[6];
    $nid    = $row[7];
    $nnom   = $row[8];
    $cest   = $row[9];
    $ecid   = $row[10];
    $ecnom  = $row[11];
    $cpa    = $row[12];
    $fecha_mod    = $row[13];
    $tipoSui    = $row[14];
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>
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
               
        $("#sltFechaM").datepicker({changeMonth: true,}).val();
        
        
});
</script>
<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
<title>Modificar Categoría</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Categoría</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarCategoriaJson.php">
                              <input type="hidden" name="id" value="<?php echo $cid  ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
<!----------Campo para llenar Codigo Interno-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="codigoadmin" class="col-sm-5 control-label"><strong class="obligado">*</strong>Código Interno:</label>
                     <input type="text" name="txtCodigoI" id="txtCodigoI" class="form-control" value="<?php echo $ccod?>" maxlength="100" title="Ingrese el código interno" onkeypress="return txtValida(event,'num_car')" placeholder="Código Interno" required>
                </div>                                    
<!----------Fin Campo Codigo Interno-->
<!----------Campo para llenar Nombre-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="Nombre" class="col-sm-5 control-label"><strong class="obligado"></strong>Nombre:</label>
                     <input type="text" name="txtNombre" id="txtNombre" class="form-control" value="<?php echo $cnom?>" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre">
                </div>                                    
<!----------Fin Campo Nombre-->
<!----------Campo para llenar Salario Actual-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="SalarioAC" class="col-sm-5 control-label"><strong class="obligado"></strong>Salario Actual:</label>
                     <input type="text" name="txtSalarioAC" id="txtSalarioAC" class="form-control" value="<?php echo $csalac?>" maxlength="100" title="Ingrese el salario actual" onkeypress="return txtValida(event,'num')" placeholder="Salario Actual">
                </div>                                    
<!----------Fin Campo Salario Actual-->
<!----------Campo para llenar Salario Anterior-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="SalarioAN" class="col-sm-5 control-label"><strong class="obligado"></strong>Salario Anterior:</label>
                     <input type="text" name="txtSalarioAN" id="txtSalarioAN" class="form-control" value="<?php echo $csalan?>"maxlength="100" title="Ingrese el salario anterior" onkeypress="return txtValida(event,'num')" placeholder="Salario Anterior">
                </div>                                    
<!----------Fin Campo Salario Anterior-->
<!----------Fecha Modificacion Salario-->
<div class="form-group" style="margin-top: -10px;">
                <label for="fecha" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Modificación Salario:</label>
                <?php                                         
                    $tcfecm = $fecha_mod;
                     if(!empty($fecha_mod)||$fecha_mod!=''){
                            $tcfecm = trim($tcfecm, '"');
                            $fecha_div = explode("-", $tcfecm);
                            $anioi = $fecha_div[0];
                            $mesi  = $fecha_div[1];
                            $diai  = $fecha_div[2];
                            $tcfecm = $diai.'/'.$mesi.'/'.$anioi;
                      }else{
                            $tcfecm='';
                      }
                ?>
                 <input name="sltFechaM" id="sltFechaM" title="Ingrese Fecha Modificación" type="text" style="height: 30px" class="form-control col-sm-1"   value="<?php echo $tcfecm;?>">
               <!-- <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaM" id="sltFechaM" step="1" value="<?php echo $tcfecm;?>"> -->
           </div>
<!----------Fin Fecha Modificacion Salarior-->
<!----------Campo para llenar Gasto Representación-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="GastoR" class="col-sm-5 control-label"><strong class="obligado"></strong>Gasto Representación:</label>
                     <input type="text" name="txtGastoR" id="txtGastoR" class="form-control" value="<?php echo $cgas?>" maxlength="100" title="Ingrese el valor de gastos representación" onkeypress="return txtValida(event,'num')" placeholder="Gastos Representación">
                </div>                                    
<!-------------------------Fin campo Gasto Representación-->
<!----------Consulta para llenar campo Nivel-->                                                          
                        <?php 
                        if(empty($cniv))
                            $ni = "SELECT id_unico, nombre from gn_nivel";
                        else
                            $ni = "SELECT id_unico, nombre from gn_nivel WHERE id_unico != $cniv ORDER BY id_unico ASC";
                        
                        $niv = $mysqli->query($ni);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Nivel:
                            </label>
                            <select name="sltNivel" class="form-control" id="sltNivel" title="Seleccione nivel" style="height: 30px">
                            <option value="<?php echo $nid?>"><?php echo $nnom?></option>
                                <?php 
                                while ($filaN = mysqli_fetch_row($niv)) { ?>
                                <option value="<?php echo $filaN[0];?>"><?php echo $filaN[1]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""> </option>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Nivel-->
<!------------------------- Consulta para llenar Estado Categoría-->
                        <?php 
                        
                        if(empty($cest))
                           $es   = "SELECT id_unico, nombre FROM gn_estado_categoria WHERE id_unico != $cest ORDER BY id_unico ASC";
                        else
                            $es   = "SELECT id_unico, nombre FROM gn_estado_categoria WHERE id_unico != $cest ORDER BY id_unico ASC";
                        
                        $esta = $mysqli->query($es);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Estado Categoría:
                            </label>
                            <select name="sltEstado" class="form-control" id="sltEstado" title="Seleccione Estado" style="height: 30px">
                            <option value="<?php echo $ecid?>"><?php echo $ecnom?></option>
                                <?php 
                                while ($filaES = mysqli_fetch_row($esta)) { ?>
                                <option value="<?php echo $filaES[0];?>"><?php echo $filaES[1]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""> </option>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Estado Categoría-->
<!------------------------- Consulta para llenar tipo persona SUI-->
                           <div class="form-group" style="margin-top: -10px;">
                                <label for="tipoSui" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Persona SUI:</label>
                                <select name="tipoSui" class="select2_single form-control" id="tipoSui" title="Seleccione Tipo Persona SUI" style="height: 30px">
                                <?php if(!empty($tipoSui)){ ?>
                                        <option value="<?php echo $tipoSui?>"><?php echo $tipoSui;?></option>
                                    <?php }else{ ?>
                                        <option value="">-</option>
                                    <?php }?> 
                                    <option value="Temporal">Temporal</option>
                                    <option value="Nomina">Nomina</option>
                                    <option value="Aprendiz">Aprendiz</option>
                                    <option value="">-</option>
                                </select>
                            </div> 
<!----------Fin Consulta Para llenar tipo persona SUI-->                           
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
    </body>
</html>
