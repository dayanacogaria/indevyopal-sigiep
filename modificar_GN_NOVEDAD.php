<?php require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT        n.id_unico,
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
                        CONCAT(c.codigo,' - ',c.descripcion)
                FROM gn_novedad n	 
                LEFT JOIN	gn_empleado e ON n.empleado = e.id_unico
                LEFT JOIN   gf_tercero t ON e.tercero = t.id_unico
                LEFT JOIN   gn_periodo p ON n.periodo = p.id_unico
                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico
                where md5(n.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
        $nid    = $row[0];
        $nval   = $row[1];
        $nfec   = $row[2];
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
         
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>
<title>Modificar Novedad</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Novedad</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarNovedadJson.php">
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
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico != $empid";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select required="required" name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px">
                            <option value="<?php echo $nemp?>"><?php echo $ternom?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($empleado)) { ?>
                                <option value="<?php echo $filaT[0];?>"><?php echo ucwords(($filaT[3])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Empleado-->
<!------------------------- Campo para llenar Valor-->
                        <div class="form-group" style="margin-top: -10px;">
                                 <label for="valor" class="col-sm-5 control-label"><strong class="obligado"></strong>Valor:</label>
                                <input type="text" name="txtValor" id="txtValor" value="<?php echo $nval?>" class="form-control" maxlength="100" title="Ingrese el valor de la novedad" placeholder="Valor">
                            </div>
<!----------Fin Campo para llenar Valor-->
<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
<!------------------------- Campo para seleccionar Fecha-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fecha" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha:</label>
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFecha" id="sltFecha" step="1" value="<?php echo $nfec;?>">
           </div>
<!----------Fin Captura de Fecha-->                              
<!------------------------- Consulta para llenar campo Período-->
            <?php
            if($nper=="")
                $per = "SELECT id_unico, codigointerno FROM gn_periodo";
            else
                $per = "SELECT id_unico, codigointerno FROM gn_periodo WHERE id_unico != $nper";
            $period = $mysqli->query($per);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Período:
                </label>
                <select name="sltPeriodo" class="form-control" id="sltPeriodo" title="Seleccione período" style="height: 30px">
                <option value ="<?php echo $pid?>"><?php echo $pci?></option>
                
                    <?php 
                    while ($filaP = mysqli_fetch_row($period)) { ?>                   
                    <option value="<?php echo $filaP[0];?>"><?php echo $filaP[1];?></option>
                    <?php
                    }
                    ?>
                    <option value=""> </option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Período-->
<!------------------------- Consulta para llenar campo Concepto-->
            <?php 
            if($ncon=="")
                $con = "SELECT id_unico, CONCAT(codigo,' - ',descripcion) FROM gn_concepto";
            else
                $con = "SELECT id_unico, CONCAT(codigo,' - ',descripcion) FROM gn_concepto WHERE id_unico != $ncon";
            $concept = $mysqli->query($con);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado">*</strong>Concepto:
                </label>
                <select name="sltConcepto" class="form-control" id="sltConcepto" title="Seleccione concepto" style="height: 30px">
                <option value ="<?php echo $conid?>"><?php echo $concn?></option>                
                    <?php 
                    while ($filaC = mysqli_fetch_row($concept)) { ?>                   
                    <option value="<?php echo $filaC[0];?>"><?php echo $filaC[1];?></option>
                    <?php
                    }
                    ?>
                    <option value=""> </option>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Concepto-->                                                           
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
