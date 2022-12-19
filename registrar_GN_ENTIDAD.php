
<?php
require_once ('head.php');
require_once ('./Conexion/conexion.php');
#session_start();
?>
   <title>Registrar Entidad</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-8 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Empleado Entidad</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarEntidadJson.php">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                               
<!------------------------- Consulta para llenar campo Empleado-->
                        <?php 
                        $emp = "SELECT 						
                                                        e.id_unico,
                                                        e.tercero,
							                            t.id_unico,
                                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required>
                            <option value="">Empleado</option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($empleado)) { ?>
                                <option value="<?php echo $filaT[0];?>"><?php echo ucwords(($filaT[3])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Empleado-->
<!----------Campo para llenar Nombre-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="Nombre" class="col-sm-5 control-label"><strong class="obligado"></strong>Nombre:</label>
                     <input type="text" name="txtNombre" id="txtNombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre">
                </div>                                    
<!----------Fin Campo Nombre-->
<!----------Campo para llenar Dirección-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="Direccion" class="col-sm-5 control-label"><strong class="obligado"></strong>Dirección:</label>
                     <input type="text" name="txtDireccion" id="txtDireccion" class="form-control" maxlength="100" title="Ingrese la dirección" onkeypress="return txtValida(event,'direccion')" placeholder="Dirección">
                </div>                                    
<!----------Fin Campo Direccion-->
<!----------Campo para llenar Teléfono-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="Telefono" class="col-sm-5 control-label"><strong class="obligado"></strong>Teléfono:</label>
                     <input type="text" name="txtTelefono" id="txtTelefono" class="form-control" maxlength="100" title="Ingrese el número telefónico" onkeypress="return txtValida(event,'num')" placeholder="Teléfono">
                </div>                                    
<!----------Fin Campo Teléfono-->
<!----------Campo para llenar Email-->
                <div class="form-group" style="margin-top: -10px;">
                     <label for="Email" class="col-sm-5 control-label"><strong class="obligado"></strong>Email:</label>
                     <input type="email" name="txtEmail" id="txtEmail" class="form-control" maxlength="100" 
                            title="Ingrese la dirección de Email" placeholder="Email" pattern="^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$">
                </div>                                    
<!----------Fin Campo Email-->
<!------------------------- Consulta para llenar campo Tipo Entidad-->
            <?php 
            $tip = "SELECT id_unico, nombre FROM gn_tipo_entidad";
            $tipe = $mysqli->query($tip);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Tipo Entidad:
                </label>
                <select name="sltTipo" class="form-control" id="sltTipo" title="Seleccione Tipo entidad" style="height: 30px">
                <option value="">Tipo Entidad</option>
                
                    <?php 
                    while ($filaTE = mysqli_fetch_row($tipe)) { ?>                   
                    <option value="<?php echo $filaTE[0];?>"><?php echo $filaTE[1];?></option>
                    <?php
                    }
                    ?>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Tipo Entidad-->
<!------------------------- Consulta para llenar campo Ubicación-->
            <?php 
            $ub = "SELECT c.id_unico, CONCAT(c.nombre,', ',d.nombre)
                    FROM gf_ciudad c
                    LEFT JOIN gf_departamento d ON c.departamento = d.id_unico";
            $ubi = $mysqli->query($ub);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado"></strong>Ubicación:
                </label>
                <select name="sltUbicacion" class="form-control" id="sltUbicacion" title="Seleccione ubicación" style="height: 30px">
                <option value="">Ubiación</option>
                
                    <?php 
                    while ($filaU = mysqli_fetch_row($ubi)) { ?>                   
                    <option value="<?php echo $filaU[0];?>"><?php echo $filaU[1];?></option>
                    <?php
                    }
                    ?>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar Ubicación-->

                            <div class="form-group" style="margin-top: 10px;">
                               <label for="no" class="col-sm-5 control-label"></label>
                               <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px;margin-left: 0px  ;">Guardar</button>
                            </div>

                          </form>
                      </div>
                  </div>                  
                  <div class="col-sm-8 col-sm-1" styl>
    
                  <div class="col-sm-8 col-sm-1" style>
            <table class="tablaC table-condensed text-center" align="center">
                        <thead>
                            <tr>
                                <tr>                                        
                                    <th>
                                        <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                    </th>
                                </tr>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_EMPLEADO.php">EMPLEADO</a>
                                </td>
                            </tr>
                            
            </table>
                  </div>
              </div>
        </div>        
        <?php require_once './footer.php'; ?>
    </body>
</html>
    