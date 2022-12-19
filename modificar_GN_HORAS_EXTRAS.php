<?php require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT        he.id_unico,
                        he.numerohoras,
                        he.fecha,
                        he.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        he.concepto,
                        c.id_unico,
                        CONCAT(c.codigo,' - ',c.descripcion)
                FROM gn_horas_extras he	 
                LEFT JOIN	gn_empleado e ON he.empleado = e.id_unico
                LEFT JOIN   gf_tercero t ON e.tercero = t.id_unico
                LEFT JOIN gn_concepto c ON he.concepto = c.id_unico                
                where md5(he.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
        $heid   = $row[0];
        $henum  = $row[1];
        $hefec  = $row[2];
        $heemp  = $row[3];
        $empid  = $row[4];
        $empter = $row[5];
        $terid  = $row[6];
        $ternom = $row[7];
        $hecon  = $row[8];
        $conid  = $row[9];
        $concn  = $row[10];
         
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>
<title>Modificar Horas Extras</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Horas Extras</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarHorasExtrasJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
<!------------------------- Consulta para llenar campo Empleado-->
                        <?php 
                        if($empid=="")
                            $emp = "SELECT 						
                                                        e.id_unico,
                                                        e.tercero,
                                                        t.id_unico,
                                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico";
                        else
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
                                    <strong class="obligado"></strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px">
                            <option value="<?php echo $empid?>"><?php echo $ternom?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($empleado)) { ?>
                                <option value="<?php echo $filaT[0];?>"><?php echo ucwords(($filaT[3])); ?></option>
                                <?php
                                }
                                ?>
                                <option value=""> </option>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Empleado-->
<!------------------------- Consulta para llenar campo Concepto-->
            <?php
            if($conid=="")
                $con = "SELECT id_unico, CONCAT(codigo,' - ',descripcion) FROM gn_concepto";
            else
                $con = "SELECT id_unico, CONCAT(codigo,' - ',descripcion) FROM gn_concepto WHERE id_unico != $conid";
            $concept = $mysqli->query($con);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Concepto:
                </label>
                <select name="sltConcepto" class="form-control" id="sltConcepto" title="Seleccione concepto" style="height: 30px">
                <option value = "<?php echo $hecon?>"><?php echo $concn?></option>                
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
<!------------------------- Campo para llenar Número de Horas-->
            <div class="form-group" style="margin-top: -10px;">
                <label for="numerohoras" class="col-sm-5 control-label"><strong class="obligado"></strong>Número de horas:</label>
            <input type="text" name="txtnumerohoras" id="txtnumerohoras" class="form-control" maxlength="100" title="Ingrese el número de horas" value="<?php echo $henum?>">
                            </div>
<!----------Fin Campo para llenar Número de Horas-->
<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
<!------------------------- Campo para seleccionar Fecha-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fecha" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha:</label>
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFecha" id="slstFecha" step="1" value="<?php echo $hefec;?>">
           </div>
<!----------Fin Captura de Fecha-->                              
                                                           
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
