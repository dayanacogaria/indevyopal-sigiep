<?php require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT    f.id_unico,
                    f.empleado,
                    e.id_unico,
                    e.tercero,
                    t.id_unico,
                    CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                    f.tiporelacion,
                    tr.id_unico,
                    tr.nombre,
                    f.tercero,
                    ter.id_unico,
                    CONCAT(ter.nombreuno,' ',ter.nombredos,' ',ter.apellidouno,' ',ter.apellidodos)
                FROM gn_familiar f	 
                LEFT JOIN	gn_empleado e ON f.empleado = e.id_unico
                LEFT JOIN   gf_tercero t ON e.tercero = t.id_unico
                LEFT JOIN   gn_tipo_relacion tr ON f.tiporelacion = tr.id_unico
                LEFT JOIN   gf_tercero ter ON f.tercero = ter.id_unico
                where md5(f.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
    $femp   = $row[1];
    $eid    = $row[2];
    $eter   = $row[3];
    $tid1   = $row[4];
    $ter1   = $row[5];
    $frel   = $row[6];
    $trid   = $row[7];
    $trnom  = $row[8];
    $fter   = $row[9];
    $tid2   = $row[10];
    $ter2   = $row[11];
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>
<title>Modificar Familiar</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Familiar</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarFamiliarJson.php">
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
                            WHERE e.id_unico != $femp";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required="">
                            <option value="<?php echo $eid?>"><?php echo $ter1?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($empleado)) { ?>
                                <option value="<?php echo $filaT[0];?>"><?php echo ucwords(($filaT[3])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Empleado-->
<!------------------------- Consulta para llenar Tipo Relación-->
                        <?php 
                        $rel = "SELECT id_unico, nombre FROM gn_tipo_relacion WHERE id_unico != $frel";
                        $relac = $mysqli->query($rel);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Relación:
                            </label>
                            <select name="sltRelacion" class="form-control" id="sltRelacion" title="Seleccione Relación" style="height: 30px" required="">
                            <option value="<?php echo $trid?>"><?php echo $trnom?></option>
                                <?php 
                                while ($filaR = mysqli_fetch_row($relac)) { ?>
                                <option value="<?php echo $filaR[0];?>"><?php echo $filaR[1]; ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Tipo Relación-->
<!------------------------- Consulta para llenar campo Tercero-->
                        <?php 
                        $ter = "SELECT          t.id_unico,
                                                CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                            FROM gf_perfil_tercero pt 
                            LEFT JOIN gf_tercero t  ON pt.tercero = t.id_unico
                            WHERE pt.perfil = 10 AND id_unico != $fter";
                        $tercero = $mysqli->query($ter);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Tercero:
                            </label>
                            <select name="sltTercero" class="form-control" id="sltTercero" title="Seleccione tercero" style="height: 30px" required="">
                            <option value="<?php echo $tid2?>"><?php echo $ter2?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($tercero)) { ?>
                                <option value="<?php echo $filaT[0];?>"><?php echo $filaT[1]; ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Tercero-->                              
                                                           
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
