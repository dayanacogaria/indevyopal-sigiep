<?php require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT        de.id_unico,
                        de.ruta,
                        de.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        de.documento,
                        td.id_unico,
                        td.nombre
                FROM gn_documentacion_empleado de	 
                LEFT JOIN	gn_empleado e ON de.empleado = e.id_unico
                LEFT JOIN   gf_tercero t    ON e.tercero = t.id_unico
                LEFT JOIN	gf_tipo_documento td ON de.documento = td.id_unico
                where md5(de.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
    $ruta   = $row[1];
    $demp   = $row[2];
    $idter  = $row[5];
    $terc   = $row[6];
    $detd   = $row[7];
    $detnom = $row[9];
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>
<title>Modificar Documentación Empleado</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Documentación Empleado</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarDocumentacionEmpleadoJson.php">
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
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.tercero != $idter";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required="">
                            <option value="<?php echo $demp;?>"><?php echo $terc?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($empleado)) { ?>
                                <option value="<?php echo $filaT[0];?>"><?php echo ucwords(($filaT[3])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Empleado-->                                                           
<!------------------------- Consulta para llenar campo Documento-->
            <?php 
            $sql = "SELECT id_unico, nombre FROM gf_tipo_documento where id_unico != '$detd'";
            $documento = $mysqli->query($sql);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado">*</strong>Documento:
                </label>
                <select name="sltDocumento" class="form-control" id="sltDocumento" title="Seleccione tipo de documento" style="height: 30px" required="">
                <option value="<?php echo $detd;?>"><?php echo $detnom;?>
                </option>
                
                    <?php 
                    while ($filad = mysqli_fetch_row($documento)) { ?>
                   
                    <option value="<?php echo $filad[0];?>"><?php echo $filad[1];?></option>
                    <?php
                    }
                    ?>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Documento-->
<!------------------------- Campo para llenar Ruta----------------------->
                        <div class="form-group" style="margin-top: -10px;">
                                 <label for="nombre" class="col-sm-5 control-label"><strong class="obligado"></strong>Ruta:</label>
                                <input type="text" name="txtRuta" id="txtRuta" value="<?php echo $ruta?>" class="form-control" maxlength="100" title="Ingrese la ruta" placeholder="Ruta">
                            </div>
<!------------------------ Fin Campo para llenar ruta-------------------->                              
                                                           
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
