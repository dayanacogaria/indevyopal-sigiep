<?php require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
  $id = (($_GET["id"]));

  $sql = "SELECT        l.id_unico,
                        l.fechaingreso,
                        l.fecharetiro,
                        l.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        l.entidad,
                        en.id_unico,
                        en.nombre,
                        l.dependencia,
                        de.id_unico,
                        de.nombre,
                        l.cargo,
                        ca.id_unico,
                        ca.nombre,
                        l.causaretiro,
                        cr.id_unico,
                        cr.nombre,
                        l.tipodedicacion,
                        td.id_unico,
                        td.nombre
                FROM gn_laboral l	 
                LEFT JOIN	gn_empleado e           ON l.empleado       = e.id_unico
                LEFT JOIN   gf_tercero t            ON e.tercero        = t.id_unico
                LEFT JOIN   gn_entidad en           ON l.entidad        = en.id_unico
                LEFT JOIN   gn_dependencia_empleado de ON l.dependencia = de.id_unico
                LEFT JOIN   gf_cargo ca             ON l.cargo          = ca.id_unico
                LEFT JOIN   gn_causa_retiro cr      ON l.causaretiro    = cr.id_unico
                LEFT JOIN   gn_tipo_dedicacion td   ON l.tipodedicacion = td.id_unico
                where md5(l.id_unico) = '$id'";
  $resultado = $mysqli->query($sql);
  $row = mysqli_fetch_row($resultado);    
    
        $lid    = $row[0];
        $lfi    = $row[1];
        $lfr    = $row[2];
        $lemp   = $row[3];
        $empid  = $row[4];
        $empter = $row[5];
        $terid  = $row[6];
        $ternom = $row[7];
        $len    = $row[8];
        $enid   = $row[9];
        $enom   = $row[10];
        $ldep   = $row[11];
        $deid   = $row[12];
        $denom  = $row[13];
        $lca    = $row[14];
        $caid   = $row[15];
        $canom  = $row[16];
        $lcr    = $row[17];
        $crid   = $row[18];
        $crnom  = $row[19];        
        $ltd    = $row[20];
        $tdid   = $row[21];
        $tdnom  = $row[22];
        
         
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>
<title>Modificar Laboral</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Laboral</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarLaboralJson.php">
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
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico != $lemp";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required="">
                            <option value="<?php echo $lemp?>"><?php echo $ternom?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($empleado)) { ?>
                                <option value="<?php echo $filaT[0];?>"><?php echo ucwords(($filaT[3])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Empleado-->
<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
<!------------------------- Campo para seleccionar Fecha Ingreso-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="FechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Ingreso:</label>
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaI" id="sltFechaI" step="1" max="2016-12-31" value="<?php echo $lfi?>">
           </div>
<!----------Fin Captura de Fecha Ingreso-->                               
<!------------------------- Campo para seleccionar Fecha Retiro-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="FechaR" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Retiro:</label>
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaR" id="sltFechaR" step="1" max="2016-12-31" value="<?php echo $lfr;?>">
           </div>
<!----------Fin Captura de Fecha Retiro-->                              
<!------------------------- Consulta para llenar campo Entidad-->
            <?php 
            
            if(empty($len))
                $en = "SELECT id_unico, nombre FROM gn_entidad";
            else
                $en = "SELECT id_unico, nombre FROM gn_entidad WHERE id_unico != $len";
            
            $ent = $mysqli->query($en);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Entidad:
                </label>
                <select name="sltEntidad" class="form-control" id="sltEntidad" title="Seleccione entidad" style="height: 30px">
                <option value="<?php echo $enid?>"><?php echo $enom?></option>
                
                    <?php 
                    while ($filaE = mysqli_fetch_row($ent)) { ?>                   
                    <option value="<?php echo $filaE[0];?>"><?php echo $filaE[1];?></option>
                    <?php
                    }
                    ?>
                    <option value=""> </option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Entidad-->
<!------------------------- Consulta para llenar campo Tipo Dedicación-->
            <?php 
            
            if(empty($ltd))
                $tip = "SELECT id_unico, nombre FROM gn_tipo_dedicacion";
            else
                $tip = "SELECT id_unico, nombre FROM gn_tipo_dedicacion WHERE id_unico != $ltd";
            
            $tipod = $mysqli->query($tip);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Tipo Dedicación:
                </label>
                <select name="sltTipo" class="form-control" id="sltTipo" title="Seleccione tipo dedicación" style="height: 30px">
                <option value="<?php echo $tdid;?>"><?php echo $tdnom;?></option>                
                    <?php 
                    while ($filaTD = mysqli_fetch_row($tipod)) { ?>                   
                    <option value="<?php echo $filaTD[0];?>"><?php echo $filaTD[1];?></option>
                    <?php
                    }
                    ?>
                    <option value=""> </option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Tipo Dedicación-->
<!------------------------- Consulta para llenar campo Dependencia Empleado-->
            <?php 
            
            if(empty($ldep))
                $dep = "SELECT id_unico, nombre FROM gn_dependencia_empleado";
            else
                $dep = "SELECT id_unico, nombre FROM gn_dependencia_empleado WHERE id_unico != $ldep";
            
            $depen = $mysqli->query($dep);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado">*</strong>Dependencia Empleado:
                </label>
                <select name="sltDependencia" class="form-control" id="sltDependencia" title="Seleccione Dependencia Empleado" style="height: 30px">
                <option value="<?php echo $deid;?>"><?php echo $denom;?></option>
                
                    <?php 
                    while ($filaD = mysqli_fetch_row($depen)) { ?>                   
                    <option value="<?php echo $filaD[0];?>"><?php echo $filaD[1];?></option>
                    <?php
                    }
                    ?>
                    <option value=""> </option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Tipo Dedicación-->
<!------------------------- Consulta para llenar campo Cargo-->
            <?php 
            
            if(empty($lca))
                $car = "SELECT id_unico, nombre FROM gf_cargo";
            else
                $car = "SELECT id_unico, nombre FROM gf_cargo WHERE id_unico !=$lca";
            
            $carg = $mysqli->query($car);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Cargo:
                </label>
                <select name="sltCargo" class="form-control" id="sltCargo" title="Seleccione Cargo" style="height: 30px">
                <option value="<?php echo $caid;?>"><?php echo $canom;?></option>
                
                    <?php 
                    while ($filaC = mysqli_fetch_row($carg)) { ?>                   
                    <option value="<?php echo $filaC[0];?>"><?php echo $filaC[1];?></option>
                    <?php
                    }
                    ?>
                    <option value=""></option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Cargo-->
<!------------------------- Consulta para llenar campo Causa Retiro-->
            <?php 
            
            if(empty($lcr))
            $cau = "SELECT id_unico, nombre FROM gn_causa_retiro";
            else
            $cau = "SELECT id_unico, nombre FROM gn_causa_retiro WHERE id_unico !=$lcr";
            
            $caur = $mysqli->query($cau);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Causa Retiro:
                </label>
                <select name="sltCausaR" class="form-control" id="sltCausaR" title="Seleccione Causa Retiro" style="height: 30px">
                <option value="<?php echo $crid;?>"><?php echo $crnom;?></option>
                
                    <?php 
                    while ($filaCR = mysqli_fetch_row($caur)) { ?>                   
                    <option value="<?php echo $filaCR[0];?>"><?php echo $filaCR[1];?></option>
                    <?php
                    }
                    ?>
                    <option value=""></option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Causa Retiro-->
                                                           
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
