<?php require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
  $id = (($_GET["id"]));

  $sql = "SELECT        en.id_unico,
                        en.numeroacto,
                        en.fechaacto,
                        en.fechainicio,
                        en.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        en.fechafin,
                        en.categoria,
                        c.id_unico,
                        c.nombre,
                        en.cargo,
                        car.id_unico,
                        car.nombre,
                        en.dependencia,
                        d.id_unico,
                        d.nombre,
                        en.tiponovedad,
                        tn.id_unico,
                        tn.nombre
                FROM gn_encargo en
                LEFT JOIN	gn_empleado e         ON en.empleado    = e.id_unico
                LEFT JOIN   gf_tercero t          ON e.tercero      = t.id_unico
                LEFT JOIN   gn_tipo_novedad tn    ON en.tiponovedad = tn.id_unico
                LEFT JOIN   gn_categoria c        ON en.categoria   = c.id_unico
                LEFT JOIN   gf_cargo car          ON en.cargo       = car.id_unico
                LEFT JOIN   gf_tipo_dependencia d ON en.dependencia = d.id_unico
                where md5(en.id_unico) = '$id'";

  $resultado = $mysqli->query($sql);
  $row = mysqli_fetch_row($resultado);    
    
        $enid   = $row[0];
        $ennact = $row[1];
        $enfact = $row[2];
        $enfi   = $row[3];
        $enemp  = $row[4];
        $empid  = $row[5];
        $empter = $row[6];
        $terid  = $row[7];
        $ternom = $row[8];
        $enff   = $row[9];
        $encat  = $row[10];
        $cid    = $row[11];
        $cnom   = $row[12];
        $encar  = $row[13];
        $carid  = $row[14];
        $carnom = $row[15];
        $endep  = $row[16];
        $depid  = $row[17];
        $depnom = $row[18];
        $entn   = $row[19];
        $tnid   = $row[20];
        $tnnom  = $row[21];
        
         
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>
<title>Modificar Encargo</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Encargo</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarEncargoJson.php">
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
                            WHERE id_unico != $enemp";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required="">
                            <option value="<?php echo $terid?>"><?php echo $ternom?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($empleado)) { ?>
                                <option value="<?php echo $filaT[0];?>"><?php echo ucwords(($filaT[3])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Empleado-->

<!------------------------- Consulta para llenar campo Categoría-->
            <?php 
            $ca = "SELECT id_unico, nombre FROM gn_categoria WHERE id_unico != $encat";
            $cat = $mysqli->query($ca);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado">*</strong>Categoría:
                </label>
                <select name="sltCategoria" class="form-control" id="sltTipo" title="Seleccione categoría" style="height: 30px" required="">
                <option value="<?php echo $cid?>"><?php echo $cnom?></option>
                
                    <?php 
                    while ($filaCAT = mysqli_fetch_row($cat)) { ?>                   
                    <option value="<?php echo $filaCAT[0];?>"><?php echo $filaCAT[1];?></option>
                    <?php
                    }
                    ?>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Categoría-->
<!------------------------- Consulta para llenar campo Cargo-->
            <?php 
            $crg = "SELECT id_unico, nombre FROM gf_cargo WHERE id_unico != $encar";
            $carg = $mysqli->query($crg);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado">*</strong>Cargo:
                </label>
                <select name="sltCargo" class="form-control" id="sltCargo" title="Seleccione cargo" style="height: 30px" required="">
                <option value="<?php echo $carid?>"><?php echo $carnom?></option>
                
                    <?php 
                    while ($filaCAR = mysqli_fetch_row($carg)) { ?>                   
                    <option value="<?php echo $filaCAR[0];?>"><?php echo $filaCAR[1];?></option>
                    <?php
                    }
                    ?>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Cargo-->
<!------------------------- Consulta para llenar campo Dependencia-->
            <?php 
            $de = "SELECT id_unico, nombre FROM gf_tipo_dependencia WHERE id_unico != $endep";
            $dep = $mysqli->query($de);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado">*</strong>Dependencia:
                </label>
                <select name="sltDependencia" class="form-control" id="sltDependencia" title="Seleccione dependencia" style="height: 30px" required="">
                <option value="<?php echo $depid?>"><?php echo $depnom?></option>
                
                    <?php 
                    while ($filaD = mysqli_fetch_row($dep)) { ?>                   
                    <option value="<?php echo $filaD[0];?>"><?php echo $filaD[1];?></option>
                    <?php
                    }
                    ?>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Dependencia-->
<!------------------------- Consulta para llenar campo Tipo Novedad-->
            <?php 
            $tip = "SELECT id_unico, nombre FROM gn_tipo_novedad WHERE id_unico != $entn";
            $tipon = $mysqli->query($tip);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado">*</strong>Tipo Novedad:
                </label>
                <select name="sltTipo" class="form-control" id="sltTipo" title="Seleccione tipo vinculación" style="height: 30px" required="">
                <option value="<?php echo $row[20]?>"><?php echo $row[21]?></option>
                
                    <?php 
                    while ($filaTN = mysqli_fetch_row($tipon)) { ?>                   
                    <option value="<?php echo $filaTN[0];?>"><?php echo $filaTN[1];?></option>
                    <?php
                    }
                    ?>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Tipo Novedad-->
<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
<!------------------------- Campo para seleccionar Fecha Inicio-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="FechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Inicial:</label>
                <input style="width:auto" class="col-sm-2 input-sm" type="date" value="<?php echo $enfi?>" name="sltFechaI" id="sltFechaI" step="1" max="2016-12-31" value="<?php echo date("Y-m-d");?>">
           </div>
<!----------Fin Captura de Fecha Inicio-->                                                            
<!------------------------- Campo para seleccionar Fecha Fin-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="FechaF" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Fin:</label>
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaF" id="sltFechaF" value="<?php echo $enff?>" step="1" max="2016-12-31" value="<?php echo date("Y-m-d");?>">
           </div>
<!----------Fin Captura de Fecha Fin-->                              
<!------------------------- Campo Llenar Número Acto-->
                            <div class="form-group" style="margin-top: -10px;">
                                 <label for="numeroA" class="col-sm-5 control-label"><strong class="obligado"></strong>Número Acto:</label>
                                <input type="text" name="txtNumeroA" id="txtNumeroA" value="<?php echo $ennact?>" class="form-control" maxlength="100" title="Ingrese el número de acto" onkeypress="return txtValida(event,'num_car')" placeholder="Número Acto">
                            </div>                              
<!------------------------- Fin Campo Llenar Número Acto-->
<!------------------------- Campo para seleccionar Fecha Acto-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="FechaA" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Acto:</label>
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaA" value="<?php echo $enfact?>" id="sltFechaA" step="1" max="2016-12-31" value="<?php echo date("Y-m-d");?>">
           </div>
<!----------Fin Captura de Fecha Acto-->   
                                                           
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
