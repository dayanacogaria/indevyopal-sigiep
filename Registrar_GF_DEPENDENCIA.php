<?php 
# ************************************************************************************
# 09/11/18 : cambio en las consultas para el select de centro de costo y de predecesor
# ************************************************************************************
    require_once 'head.php';
    require_once('Conexion/conexion.php');
    $compania = $_SESSION['compania'];
    $anno     = $_SESSION['anno'];
?>
    <!--Titulo de la página-->
    <title>Registrar Dependencia</title>
        <!-- select2 -->
    <link href="css/select/select2.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.js" rel="stylesheet">
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left" style="margin-top: -10px">
                <!--Titulo del formulario-->
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 10px; margin-right: 4px; margin-left: 4px;">Registrar Dependencia</h2>
                <a href="LISTAR_GF_DEPENDENCIA.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">Dependencia
                    </h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="controller/controllerGFDependencia.php?action=insert">
                        <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <!--Ingresa la información-->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="100" title="Ingrese el nombre"  placeholder="Nombre" required>                            
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="sigla" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Sigla:</label>
                            <input type="text" name="sigla" id="sigla" class="form-control" placeholder="Sigla" onkeypress="return txtValida(event,'sin_espcio')" style="text-transform:uppercase;" maxlength="10" title="Ingrese sigla"  >                            
                        </div>
                        <div class="form-group" style="margin-top: -10px" >
                            <label for="movimiento" class="col-sm-5 control-label" style="margin-top:-4px;"><strong style="color:#03C1FB; ">*</strong>Movimiento:</label>
                            <input type="radio" name="movimiento" value="0" title="Seleccione si es auxiliar para movimiento">Sí
                            <input type="radio" name="movimiento" value="1" title="Seleccione si no es auxiliar para movimiento" checked>No
                        </div>
                        <div class="form-group" >
                            <label for="activa" class="col-sm-5 control-label" style="margin-top:-4px;"><strong style="color:#03C1FB;">*</strong>Activa:</label>
                            <input type="radio" name="activa" value="0" title="Seleccione si esta activa">Sí
                            <input type="radio" name="activa" value="1" title="Seleccione si no esta activa" checked>No
                        </div>
                        <div class="form-group" >
                            <!-- Busqueda rapida Predecesor -->
                            <input type="hidden" id="predecesor" name="predecesor" title="Seleccione predecesor">
                            <label for="predecesor" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Predecesor:</label>
                            <!-- Consulta campo predecesor-->
                          <?php 
                            $pre= "SELECT id_unico, nombre , sigla 
                                   FROM gf_dependencia 
                                   WHERE compania = $compania
                                   ORDER BY nombre ASC";
                            $pred = $mysqli->query($pre);
                          ?>  
                            <select name="predecesor" id="predecesorSelect" title="Seleccione predecesor" class="select2_single form-control" >
                                <option value="">Predecesor</option>
                                <?php while ($rowP = mysqli_fetch_row($pred)){ ?>
                                    <option value="<?php echo $rowP[0];?>"><?php echo $rowP[2].' - '.ucwords(strtolower($rowP[1]));?></option>
                                <?php } ?>
                            </select>                            
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                             <!-- Busqueda rapida Centro de costo -->
                            <input type="hidden" id="ccosto" name="ccosto" required="required" title="Seleccione centro de costo">
                            <label for="centroC" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Centro Costo:</label>
                            <!-- Consulta campo centro costro-->
                            <?php 
                            $centr= "SELECT id_unico, nombre 
                                     FROM gf_centro_costo 
                                     WHERE parametrizacionanno = $anno 
                                     ORDER BY nombre ASC";
                            $centroC = $mysqli->query($centr);
                            ?>
                            <select name="centroC" id="centroSelect" title="Seleccione centro de costo" class="select2_single form-control">
                                <option value="">Centro Costo</option>
                                <?php while ($rowCC = mysqli_fetch_row($centroC)){ ?>
                                    <option value="<?php echo $rowCC[0];?>"><?php echo ucwords(strtolower($rowCC[1]));?></option>
                                <?php } ?>
                           </select>                        
                        </div>
                        <div class="form-group" style="margin-top: -10px">
                            <!-- Busqueda rapida Tipo de Dependencia -->
                            <input type="hidden" id="tipod" name="tipod" required="required" title="Seleccione Tipo dependencia" value ="" />
                            <label for="tipo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Dependencia:</label>
                            <!-- Consulta campo tipo dependencia-->
                            <?php 
                            $dep= "SELECT id_unico, nombre FROM gf_tipo_dependencia ORDER BY nombre ASC";
                            $depen = $mysqli->query($dep);
                            ?>
                            
                            <select name="tipo" id="tipo" title="Seleccione tipo dependencia" class="select2_single form-control" required="required">
                                <option value="">Tipo Dependencia</option>
                                <?php while ($rowTD = mysqli_fetch_row($depen)){ ?>
                                    <option value="<?php echo $rowTD[0];?>"><?php echo ucwords(strtolower($rowTD[1]));?></option>
                                <?php } ?>
                            </select>                            
                        </div>
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                </div>
            </div>
            <div class="col-sm-3 col-sm-3" style="margin-top:-12px">
                <table class="tablaC table-condensed" >
                    <thead>
                        <tr>
                            <th><h2 class="titulo" align="center">Consultas</h2></th>
                            <th><h2 class="titulo" align="center" style=" font-size:17px;">Adicional</h2></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td align="center">
                                <div class="btnConsultas" style="margin-bottom: 1px;"><a href="#">MOVIMIENTOS <br/>ALMACÉN</a></div>
                            </td>
                            <td>
                                <button class="btn btn-primary btnInfo" disabled="true">RESPONSABLE</button>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <div class="btnConsultas" style="margin-bottom: 1px;"><a href="#"> ELEMENTOS DEVOLUTIVOS</a></div>
                            </td>
                            <td>
                                <a href="registrar_CENTRO_COSTO.php" class="btn btn-primary btnInfo">CENTRO DE COSTO</a>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <div class="btnConsultas" style="margin-bottom: 1px;"><a href="#"> <br/>COMPRAS</a></div>
                            </td>
                        </tr>
                    </tbody>
                </table>                
            </div>
        </div>
    </div>
    <?php require_once 'footer.php';?> 
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript"></script>
    </script>
    
    <script src="js/select/select2.full.js"></script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>
</body>
</html>

