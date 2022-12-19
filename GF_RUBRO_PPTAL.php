<?php 
    #Llamamos la clase conexión
    require_once ('Conexion/conexion.php');
    #Creamos las sesion
    session_start();
    #Llamamos a la cabeza
    require_once ('head.php');
?>
        <title>Registrar Rubro Presupuestal</title>
    </head>
    <body>
        <div class="container-fluid text-left">
            <div class="row content">
                <?php require_once ('menu.php'); ?>                
                <div class="col-sm-10 text-left" style="margin-top: -22px">
                    <h2 class="tituloform" align="center">Registrar Rubro Presupuestal</h2>
                    <div class="contenedorForma client-form" style="margin-top: -5px">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="#">
                            <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>					
                            <div class="form-group" style="margin-top:-20px">
                                <label for="txtNombre" class="control-label col-sm-5" >
                                    <strong class="obligado">*</strong>Nombre:                                   
                                </label>
                                <input type="text" name="txtNombre" id="txtNombre" class="form-control" title="Ingrese nombre" onkeypress="return txtValida(event,'car')" maxlength="100" placeholder="Nombre" required style="height: 30px"/>
                            </div>
                            <div class="form-group" style="margin-top:-20px">
                                <label for="txtCodigoP" class="control-label col-sm-5">
                                    Código Presupuesto:
                                </label>
                                <input type="text" name="txtCodigoP" id="txtCodigoP" class="form-control" title="Ingrese código presupuestal" onkeypress="return txtValida(event,'car')" maxlength="20" placeholder="Código Presupuestal" style="height: 30px"/>
                            </div>
                            <div class="form-group form-horizontal" style="margin-top:-20px">                                    
                                <label class="control-label col-sm-5" for="optMov">
                                    Movimiento:
                                </label>
                                <input type="radio" name="optMov" id="optMov"  title="Indicar si hay movimiento" value="1"/>SI
                                <input type="radio" name="optMov" id="optMov"  title="Indicar no hay movimiento" value="2"/>NO
                                <label for="optManP" class="control-label col-sm-offset-1">
                                    ManPac:
                                </label>
                                <input type="radio" name="optManP" id="optManP" title="Indicar si maneja PAC" value="1" />SI
                                <input type="radio" name="optManP" id="optManP" title="Indicar no maneja PAC" value="2" />NO                                    
                            </div>
                            <div class="form-group" style="margin-top:-10px;">
                                <label for="txtVigencia" class="col-sm-5 control-label">
                                    Vigencia:
                                </label>
                                <input type="text" name="txtVigencia" id="txtVigencia" title="Ingrese vigencia" class="form-control" onkeypress="return txtValida(event,'car')" maxlength="4" placeholder="Vigencia" style="height: 30px"/>
                            </div>
                            <div class="form-group" style="margin-top:-30px">
                                <label for="txtDinamica" class="col-sm-5 control-label">
                                    Dinamica:
                                </label>
                                <textarea type="text" name="txtDinamica" id="txtDinamica" title="Ingrese dinamica" class="form-control" onkeypress="return txtValida(event,'num_car')" maxlength="5000" placeholder="Dinamica" style="height: 51px;resize: both;" ></textarea>
                            </div>
                            <div class="form-group" style="margin-top:-20px">
                                <label for="" class="control-label col-sm-5">
                                    Tipo Clase:
                                </label>
                                <?php 
                                #Consulta para cargar tipo Clase
                                $sql = "SELECT id_unico,nombre FROM gf_tipo_clase_pptal ORDER BY nombre ASC";
                                #Ejecutamos la consulta cargandola en la conexión
                                $tipoC = $mysqli->query($sql);
                                #Defimos la variable fila como array o vector númerico                                
                                ?>
                                <select name="sltTipoClase" class="form-control" title="Seleccione tipo clase" style="height: 30px">
                                    <option>Tipo Clase</option>
                                    <?php 
                                    while ($fila = mysql_fetch_array($tipoC)) { ?>
                                        <option value="<?php echo $fila[0]; ?>"><?php echo $fila[1]; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -20px">
                                <label class="control-label col-sm-5">
                                    Predecesor:
                                </label>
                                <select name="sltPredecesor" class="form-control" title="Seleccione predecesor" style="height: 30px">
                                    <option>Predecesor</option>
                                </select>
                            </div>
                            <?php 
                            $sql = "SELECT id_unico, nombre FROM gf_destino ORDER BY nombre ASC";
                            $destino = $mysqli->query($sql);
                            ?>
                            <div class="form-group" style="margin-top: -20px">
                                <label class="control-label col-sm-5">
                                    Destino:
                                </label>
                                <select name="sltDestino" class="form-control" title="Seleccione destino" style="height: 30px">
                                    <option>Destino</option>
                                    <?php 
                                    while ($fila1 = mysqli_fetch_row($destino)) { ?>
                                        <option value="<?php echo $fila1[0];?>"><?php echo $fila1[1]; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php 
                            $sql = "SELECT id_unico,nombre FROM gf_tipo_vigencia ORDER BY nombre ASC"; 
                            $tipoV = $mysqli->query($sql);      
                            ?>
                            <div class="form-group" style="margin-top:-20px">
                                <label class="control-label col-sm-5">
                                    Tipo Vigencia:
                                </label>
                                <select name="stlTipoVigencia" class="form-control" title="Seleccione tipo vigencia" style="height: 30px">
                                    <option>Tipo Vigencia</option>
                                    <?php while ($fila2 = mysql_fetch_array($tipoV)) { ?>
                                    <option value="<?php echo $fila2[0]; ?>"><?php $fila2[1] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php 
                            $sql = "SELECT id_unico, nombre FROM gf_sector ORDER BY nombre ASC";
                            $sect = $mysqli->query($sql);
                            ?>
                            <div class="form-group" style="margin-top: -20px" style="height: 30px">
                                <label class="control-label col-sm-5">
                                    Sector:
                                </label>
                                <select class="form-control" name="stlSector" id="stlSector" name="Seleccione secor">
                                    <option>Sector</option>
                                    <?php while ($fila3 = mysql_fetch_row($sect)) { ?>
                                    <option value="<?php echo $fila3[0]; ?>"><?php echo $fila3[1]; ?></option>
                                    <?php   } ?>
                                </select>
                            </div>
                            <div align="center">
                                <button type="submit" class="btn btn-primary sombra" style="margin-top: -18px; margin-bottom: 10px; margin-left: -100px;" >Guardar</button>
                            </div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once('footer.php'); ?>
    </body>
</html>
