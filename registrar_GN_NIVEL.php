
<?php
require_once ('head.php');
require_once ('./Conexion/conexion.php');
#session_start();
?>
   <title>Registrar Nivel</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-8 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Nivel</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-top:-5px; margin-left: -10px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarNivelJson.php">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
<!-----Campo para llenar Nombre---->                              
                                <div class="form-group" style="margin-top: -10px;">
                                 <label for="nombre" class="col-sm-5 control-label"><strong class="obligado"></strong>Nombre:</label>
                                <input type="text" name="txtNombre" id="txtNombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre">
                            </div>  
<!--Fin Campo para llenar Nombre---->
<!------------------------- Consulta para llenar Equivalente SIA-->
                        <?php 
                        $sia = "SELECT id_unico, nombre FROM gn_equivalente_sia";
                        $equivalente = $mysqli->query($sia);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Equivalente SIA:
                            </label>
                            <select name="sltEquivalentesia" class="form-control" id="sltEquivalentesia" title="Seleccione equivalente sia" style="height: 30px">
                            <option value="">Equivalente SIA</option>
                                <?php 
                                while ($filaE = mysqli_fetch_row($equivalente)) { ?>
                                <option value="<?php echo $filaE[0];?>"><?php echo $filaE[1]; ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Equivalente SIA-->
<!------------------------- Consulta para llenar Código CGR-->
                        <?php 
                        $cgr = "SELECT id_unico, nombre FROM gn_codigo_cgr";
                        $codigo = $mysqli->query($cgr);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Código CGR:
                            </label>
                            <select name="sltCodigocgr" class="form-control" id="sltCodigocgr" title="Seleccione código CGR" style="height: 30px">
                            <option value="">Código CGR</option>
                                <?php 
                                while ($filaC = mysqli_fetch_row($codigo)) { ?>
                                <option value="<?php echo $filaC[0];?>"><?php echo $filaC[1]; ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Código CGR-->
<!------------------------- Consulta para llenar Estado Nivel-->
                        <?php 
                        $niv = "SELECT id_unico, nombre FROM gn_estado_nivel";
                        $estado = $mysqli->query($niv);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Estado Nivel:
                            </label>
                            <select name="sltEstadonivel" class="form-control" id="sltEstadonivel" title="Seleccione estado nivel" style="height: 30px">
                            <option value="">Estado Nivel</option>
                                <?php 
                                while ($filaEN = mysqli_fetch_row($estado)) { ?>
                                <option value="<?php echo $filaEN[0];?>"><?php echo $filaEN[1]; ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Estado Nivel-->     

<!------------------------- Consulta para llenar equivalente  SUI-->
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="equivalenteSui" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Equivalente SUI:</label>
                                <select name="equivalenteSui" class="select2_single form-control" id="equivalenteSui" title="Seleccione Equivalente SUI" style="height: 30px">
                                    <option value="">Seleccione Equivalente Sui</option>    
                                    <option value="Personal Directivo">Personal Directivo</option>
                                    <option value="Personal Administrativo">Personal Administrativo</option>
                                    <option value="Personal Tecnico - Operativo">Personal Tecnico - Operativo</option>
                                </select>
                            </div>    
<!----------Fin Consulta Para llenar equivalente  SUI-->  
                            <div class="form-group" style="margin-top: 10px;">
                               <label for="no" class="col-sm-5 control-label"></label>
                               <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px;margin-left: 0px  ;">Guardar</button>
                            </div>

                          </form>
                      </div>
                  </div>                  
                  <div class="col-sm-8 col-sm-1" style="margin-top:-23px">
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
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_EQUIVALENTE_SIA.php">EQUIVALENTE SIA</a>
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_CODIGO_CGR.php">CODIGO CGR</a>
                                </td>
                            </tr>
                            <tr>                                    
                                <td>
                                    <a class="btn btn-primary btnInfo" href="registrar_GN_ESTADO_NIVEL.php">ESTADO</a>
                                </td>
                            </tr>
                            
            </table>
                                </div>
          </div>
        </div>        
        <?php require_once './footer.php'; ?>
    </body>
</html>
    