<?php require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT        ie.id_unico,
                        ie.habla,
                        ie.escribe,
                        ie.lee,
                        ie.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        ie.idioma,
                        id.id_unico,
                        id.nombre
                FROM gn_idioma_empleado ie	 
                LEFT JOIN	gn_empleado e ON ie.empleado = e.id_unico
                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico                
                LEFT JOIN gn_idioma id ON ie.idioma = id.id_unico
                where md5(ie.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
                $ieid   = $row[0];
                $ieha   = $row[1];
                $iees   = $row[2];
                $iele   = $row[3];
                $ieemp  = $row[4];
                $empid  = $row[5];
                $empter = $row[6];
                $terid  = $row[7];
                $ternom = $row[8];
                $idio   = $row[9];
                $idid   = $row[10];
                $idnom  = $row[11];
         
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>
<title>Modificar Idioma Empleado</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Idioma Empleado</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarIdiomaEmpleadoJson.php">
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
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico != $ieemp";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select required="required" name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px">
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
<!------------------------- Consulta para llenar campo Idioma-->
            <?php 
            $idi = "SELECT id_unico, nombre FROM gn_idioma WHERE id_unico != $idio";
            $idiom = $mysqli->query($idi);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado">*</strong>Idioma:
                </label>
                <select required="required" name="sltIdioma" class="form-control" id="sltIdioma" title="Seleccione idioma" style="height: 30px">
                <option value ="<?php echo $idid?>"><?php echo $idnom?></option>
                
                    <?php 
                    while ($filaI = mysqli_fetch_row($idiom)) { ?>                   
                    <option value="<?php echo $filaI[0];?>"><?php echo $filaI[1];?></option>
                    <?php
                    }
                    ?>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Idioma-->
<!------------------------- Campo para llenar Habla-->
                        <div class="form-group" style="margin-top: -10px;">
                                 <label for="habla" class="col-sm-5 control-label"><strong class="obligado">*</strong>Habla:</label>
                                <input required="required" type="text" name="txtHabla" id="txtHabla" value="<?php echo $ieha?>" class="form-control" maxlength="100" title="Ingrese el nivel de habla" placeholder="Habla">
                            </div>
<!----------Fin Campo para llenar Número de Habla-->
<!------------------------- Campo para llenar Escribe-->
                        <div class="form-group" style="margin-top: -10px;">
                                 <label for="escribe" class="col-sm-5 control-label"><strong class="obligado">*</strong>Escribe:</label>
                                <input  required="required" type="text" name="txtEscribe" id="txtEscribe" value="<?php echo $iees?>" class="form-control" maxlength="100" title="Ingrese el nivel de escritura" placeholder="Escritura">
                            </div>
<!----------Fin Campo para llenar Escribe-->
<!------------------------- Campo para llenar Lee-->
                        <div class="form-group" style="margin-top: -10px;">
                                 <label for="lee" class="col-sm-5 control-label"><strong class="obligado">*</strong>Lee:</label>
                                <input required="required" type="text" name="txtLee" id="txtLee" value="<?php echo $iele?>" class="form-control" maxlength="100" title="Ingrese el nivel de lectura" placeholder="Ecuación">
                            </div>
<!----------Fin Campo para llenar Lee-->                                                  
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
