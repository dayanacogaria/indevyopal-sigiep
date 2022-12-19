<?php
require_once ('Conexion/conexion.php');

$id= $_GET['id'];
//unidad vivienda
$uv= "SELECT uv.id_unico, p.codigo_catastral, uv.codigo_interno "
        . "FROM gp_unidad_vivienda uv "
        . "LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico "
        . "WHERE md5(uv.id_unico)='$id' ";
$unidadv= $mysqli->query($uv);
$rowuv = mysqli_fetch_row($unidadv);

//TIPO SERVICIO
$tipoS= "SELECT id_unico, nombre FROM gp_tipo_servicio ORDER BY nombre ASC";
$rowTs = $mysqli->query($tipoS);

//ESTADO SERVICIO
$estadoS= "SELECT id_unico, nombre FROM gp_estado_servicio ORDER BY nombre ASC";
$rowEs = $mysqli->query($estadoS);

//LISTAR
$resul = "SELECT uvs.id_unico, uvs.unidad_vivienda, uvs.tipo_servicio, "
        . "uvs.estado_servicio, p.codigo_catastral, ts.nombre, es.nombre "
        . "FROM gp_unidad_vivienda_servicio uvs "
        . "LEFT JOIN gp_unidad_vivienda uv ON uvs.unidad_vivienda = uv.id_unico "
        . "LEFT JOIN gp_predio1 p ON uv.predio = p.id_unico "
        . "LEFT JOIN gp_tipo_servicio ts ON uvs.tipo_servicio=ts.id_unico "
        . "LEFT JOIN gp_estado_servicio es ON uvs.estado_servicio = es.id_unico "
        . "WHERE md5(uv.id_unico)='$id' ";
$resultado = $mysqli->query($resul);

require_once 'head_listar.php'; ?>
<title>Unidad Vivienda Servicio</title>
</head>
<body> 
  
    <div class="container-fluid text-center">
	<div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-8 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Unidad Vivienda Servicio</h2>
                <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px">PREDIO:<?php echo strtoupper($rowuv[1]);?><!--; CÓDIGO INTERNO: <?php //echo $rowuv[2]?>--></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">				 	
                     <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GP_UNIDAD_VIVIENDA_SERVICIOJson.php">
                        <input type="hidden" id="uv" value="<?php echo $rowuv[0]?>" name="uv">
                        <p align="center" style="margin-bottom: 25px; margin-top:0px; margin-left: 40px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline " style="margin-top: -15px;">
                            <label for="tiposervicio" class="control-label col-sm-2"><strong style="color:#03C1FB;">*</strong>Tipo Servicio:</label>
                            <select name="tiposervicio" id="tiposervicio"  style="width:200px" class="form-control col-sm-2" title="Seleccione tipo servicio" required>
                                <option value="">Tipo Servicio</option>
                                <?php while($rowtser = mysqli_fetch_row($rowTs)){?>
                                <option value="<?php echo $rowtser[0] ?>"><?php echo ucwords((strtolower($rowtser[1])));}?></option>;
                            </select> 
                            <label for="estadoservicio" class="control-label col-sm-2" style="width:170px"><strong style="color:#03C1FB;">*</strong>Estado Servicio:</label>
                           <select name="estadoservicio" id="estadoservicio" style="width:200px"  class="form-control col-sm-2" title="Seleccione estado servicio" required>
                                <option value="">Estado Servicio</option>
                                <?php while($roweser = mysqli_fetch_row($rowEs)){?>
                                <option value="<?php echo $roweser[0] ?>"><?php echo ucwords((strtolower($roweser[1])));}?></option>;
                            </select> 

                            <button type="submit" class="btn btn-primary sombra" style=" margin-left:40px; margin-top: -1px; margin-bottom: 10px; ">Guardar</button>
                            <input type="hidden" name="MM_insert" >
                        </div>
                    </form>
                </div>
               <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Tipo servicio</strong></td>
                                    <td><strong>Estado servicio</strong></td>
                                    <td><strong>Medidor</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Tipo servicio</th>
                                    <th>Estado servicio</th>
                                    <th>Medidor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while($row = mysqli_fetch_row($resultado)){?>
                                
                                <tr>
                                    <td style="display: none;"><?php echo $row[0]?></td>    
                                    <td><a  href="#" onclick="javascript:eliminar(<?php echo $row[0]?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a onclick="modificarModal(<?php echo $row[0].','.$row[2].','.$row[3].','.$row[1]?>)"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                    </td>
                                    <td><?php echo ucwords(strtolower($row[5])); ?></td>
                                    <td><?php echo ucwords(strtolower($row[6])); ?></td>  
                                    <td>
                                        <?php $uvms = "SELECT uvms.id_unico, m.id_unico, m.referencia, m.fecha_instalacion "
                                                . "FROM gp_unidad_vivienda_medidor_servicio uvms "
                                                . "LEFT JOIN gp_medidor m ON uvms.medidor = m.id_unico "
                                                . "WHERE unidad_vivienda_servicio = '$row[0]'";
                                            $car = $mysqli->query($uvms);
                                            $num=mysqli_num_rows($car);
                                            $rows= mysqli_fetch_row($car);
                                            
                                        if($num > 0 ){
                                            echo ucwords(strtoupper($rows[2])).' - '.date("d/m/Y", strtotime($rows[3]));;?>
                                            <a  href="#" onclick="javascript:eliminarMedidor(<?php echo $rows[0].','.$rows[1]?>);"><i title="Eliminar medidor" class="glyphicon glyphicon-trash"></i></a>
                                            <a href="#" onclick="javascript:mostarmodificarm(<?php echo $rows[0].','.$rows[1].','."'".$rows[3]."'"?>);"><i title="Modificar medidor" class="glyphicon glyphicon-edit" ></i></a>
                                        <?php } else { ?>
                                            <center>
                                                <a href="#"  onclick="javascript:medidor(<?php echo $row[0]?>);" class="btn btn-primary btnInfo" style="width: 80px; height: 30px" title="Asignar medidor">Medidor</a>          
                                            </center>
                                       <?php } ?>
                                        
                                       
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 text-center" align="center" style="margin-top:-15px">
                <h2 class="titulo" align="center" style=" font-size:17px;">Adicional</h2>
                <div  align="center">
                    <a href="registrar_GP_TIPO_SERVICIO.php" class="btn btn-primary btnInfo">TIPO SERVICIO</a>          
                    <a href="registrar_GP_ESTADO_SERVICIO.php" class="btn btn-primary btnInfo">ESTADO SERVICIO</a>          
                    <a href="Registrar_GP_MEDIDOR.php" class="btn btn-primary btnInfo">MEDIDOR</a>
                </div>
            </div>
	</div>
    </div>
 <!-- Eliminar Unidad vivienda servicio-->  
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de unidad vivienda servicio?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Información eliminada correctamente</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se pudo eliminar la información, el registro seleccionado esta siendo usado por otra dependencia.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
 <!-- FIN Eliminar Unidad vivienda servicio-->  
 <!-- Eliminar Unidad vivienda medidor servicio-->  
    <div class="modal fade" id="myModalME" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de unidad vivienda medidor servicio?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="verME" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
 <div class="modal fade" id="myModal1M" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Información eliminada correctamente</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1M" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal2M" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se pudo eliminar la información, el registro seleccionado esta siendo usado por otra dependencia.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2M" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
 <!-- FIN Eliminar Unidad vivienda medidor servicio-->  
<!--  MODAL y opcion  MODIFICAR  informacion  -->  
<div class="modal fade" id="myModalUpdate" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content client-form1">
      <div id="forma-modal" class="modal-header">       
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
      </div>
      <?php  //TIPO SERVICIO

$tipoSe= "SELECT id_unico, nombre FROM gp_tipo_servicio ORDER BY nombre ASC";
$rowTse = $mysqli->query($tipoSe);

//ESTADO SERVICIO
$estadoSe= "SELECT id_unico, nombre FROM gp_estado_servicio ORDER BY nombre ASC";
$rowEse = $mysqli->query($estadoSe);
?>
        <form  name="form" method="POST" action="javascript:modificarItem()">
      <div class="modal-body ">
       
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="uvv" id="uvv">
            <div class="form-group" style="margin-top: 13px;">
                <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Tipo Servicio:</label>
                <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="tipo" id="tipo" class="form-control" title="Seleccione tipo servicio" required>
                    <?php while ($modTSer = mysqli_fetch_row($rowTse)) { ?>
                          <option value="<?php echo $modTSer[0]; ?>">
                            <?php echo ucwords((strtolower($modTSer[1]))); ?>
                          </option>
                    <?php  

                     } ?>
                </select>                                
            </div>
            <div class="form-group" style="margin-top: 13px;">
                <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Estado Servicio:</label>
                <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="estados" id="estados" class="form-control" title="Seleccione estado servicio" required>
                    <?php while ($modEsS = mysqli_fetch_row($rowEse)) { ?>
                          <option value="<?php echo $modEsS[0]; ?>">
                            <?php echo ucwords((strtolower($modEsS[1]))); ?>
                          </option>
                    <?php  

                     } ?>
                </select>  
            </div>
           <input type="hidden" id="id" name="id">  
      </div>

      <div id="forma-modal" class="modal-footer">
          <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
        <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
      </div>
      </form>
    </div>
  </div>
</div>

 
<!--  MODAL para los mensajes del  modificar -->

<div class="modal fade" id="myModal5" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver5" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModal6" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La información no se ha podido modificar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver6" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal7" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>El registro ingresado ya existe.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver6" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!-- Registrar Medidor--> 
<div class="modal fade" id="myModalMedidor" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content client-form1">
        <div id="forma-modal" class="modal-header">       
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Agregar medidor</h4>
        </div>
        <?php  
        //Medidor
        $medidor= "SELECT m.id_unico, m.referencia "
                  . "FROM gp_medidor m "
                  . "WHERE NOT EXISTS(SELECT * FROM gp_unidad_vivienda_medidor_servicio WHERE medidor = m.id_unico) "
                  . "ORDER BY m.referencia ASC";
          $rowM = $mysqli->query($medidor); 
        ?>
        <form  name="form" method="POST" action="javascript:registrarMedidor()">
            <div class="modal-body ">
            <input type="hidden" name="uvs" id="uvs">
            <div class="form-group" style="margin-top: 13px;">
                <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Medidor:</label>
                <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="medidor" id="medidor" class="form-control" title="Seleccione medidor" required>
                    <option value="">Medidor</option>
                    <?php while ($modMed = mysqli_fetch_row($rowM)) { ?>
                          <option value="<?php echo $modMed[0]; ?>">
                            <?php echo strtoupper($modMed[1]); ?>
                          </option>
                    <?php  

                     } ?>
                </select>                                
            </div>
            <div class="form-group" style="margin-top: 13px;">
                <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Fecha Instalación:</label>
                <input type="date" name="fechaI" id="fechaI" class="form-control" required="required" style="display: inline; width: 250px; height:40px" >
            </div>
           <input type="hidden" id="id" name="id">  
      </div>

      <div id="forma-modal" class="modal-footer">
          <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
        <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Mensaje Registrar Medidor-->
<div class="modal fade" id="myModal8" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Información guardada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver8" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<div class="modal fade" id="myModal9" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>No se ha podido guardar la información.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver9" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
    <div class="modal fade" id="myModal10" role="dialog" align="center" >
        <div class="modal-dialog">
          <div class="modal-content">
            <div id="forma-modal" class="modal-header">
              <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>El medidor ya está asignado.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
              <button type="button" id="ver10" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
          </div>
        </div>
    </div>


  <div class="modal fade" id="myModal12" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La información no se ha podido modificar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver12" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal13" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>El medidor ya fue asignado.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver13" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--  Modal modificar medidor  -->  
<div class="modal fade" id="myModalModificarMedidor" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content client-form1">
        <div id="forma-modal" class="modal-header">       
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar medidor</h4>
        </div>
        <form  name="form" method="POST" action="javascript:modificarMedidor()">
            <div class="modal-body ">
            <div class="form-group" style="margin-top: 13px;">
                <input type="hidden" name="idm" id="idm">
                <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Medidor:</label>
                <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="medidorm" id="medidorm" class="form-control" title="Seleccione medidor" required>
                 
                </select>                                
            </div>
            <div class="form-group" style="margin-top: 13px;">
                <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Fecha Instalación:</label>
                <input type="date" name="fecham" id="fecham" class="form-control" required="required" style="display: inline; width: 250px; height:40px" >
            </div>
      </div>

      <div id="forma-modal" class="modal-footer">
          <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
        <button class="btn" id="cancelar" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Mensajes modificiación-->
<div class="modal fade" id="myModal11" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver11" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModal12" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La información no se ha podido modificar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver12" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal13" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>El medidor ya fue asignado.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver13" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
     <?php require_once 'footer.php'; ?>
 
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>

<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminar_GP_UNIDAD_VIVIENDA_SERVICIOJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true){
                      $("#myModal1").modal('show');
                      $("#ver1").click(function(){
                        document.location = 'GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo $id;?>';
                    });
                  }else{
                      $("#myModal2").modal('show');
                      $("#ver2").click(function(){
                        document.location = 'GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo $id;?>';
                    });
                  }}
              });
          });
      }
      function modificarModal(id,tipos, estados, unV){
    
            $("#tipo").val(tipos);
            $("#estados").val(estados);
            $("#id").val(id);
            $("#uvv").val(unV);
            
              $("#myModalUpdate").modal('show');
             
          }
      
      function modificarItem()
    {
      var result = '';
       var id= document.getElementById('id').value;
      var tipo= document.getElementById('tipo').value;
      var estados= document.getElementById('estados').value;
      var uvv= document.getElementById('uvv').value;
      
      $.ajax({
        type:"GET",
        url:"json/modificar_GP_UNIDAD_VIVIENDA_SERVICIOJson.php?id="+id+"&tipo="+tipo+"&estados="+estados+"&uvv="+uvv,
        success: function (data) {
          result = JSON.parse(data);
          if(result==true){
                $("#myModal5").modal('show');
                $("#ver5").click(function(){
                    document.location = 'GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo $id;?>';
                });
              }else{
                if(result=='3'){
                  $("#myModal7").modal('show');
                $("#ver7").click(function(){
                  document.location = 'GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo $id;?>';
                });
                }else {
                $("#myModal6").modal('show');
                 $("#ver6").click(function(){
                  document.location = 'GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo $id;?>';
                });
              }
              }
        }
      });
    }

 </script>
 <script>
     function medidor(id){
    
            $("#uvs").val(id);
            
              $("#myModalMedidor").modal('show');
          }
     function registrarMedidor(){
    
            var uvs= document.getElementById('uvs').value;
            var medidor= document.getElementById('medidor').value;
            var fecha= document.getElementById('fechaI').value;
      
              $.ajax({
        type:"GET",
        url:"json/registrar_GP_UV_MEDIDOR_SJson.php?uvs="+uvs+"&medidor="+medidor+"&fecha="+fecha,
        success: function (data) {
          result = JSON.parse(data);
          if(result=='1'){
                $("#myModal8").modal('show');
                $("#ver8").click(function(){
                    document.location = 'GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo $id;?>';
                });
              }else{
              if(result=='2'){
                $("#myModal10").modal('show');
                $("#ver10").click(function(){
                    document.location = 'GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo $id;?>';
                });
                } else {
                $("#myModal9").modal('show');
                $("#ver9").click(function(){
                    document.location = 'GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo $id;?>';
                });
                }
              }
        }
      });
    }
    function eliminarMedidor(id, medidor){
    var result = '';
         $("#myModalME").modal('show');
         $("#verME").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminar_GP_UV_MEDIDORSJson.php?id="+id+"&medidor="+medidor,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true) {
                      $("#myModal1M").modal('show');
                      $('#ver1M').click(function(){
                        document.location = 'GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo $id;?>';
                      });
                  } else { 
                      $("#myModal2M").modal('show');
                      $('#ver2M').click(function(){
                        document.location = 'GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo $id;?>';
                      });
                  }
                  }
              });
          });
    }
   
     
 </script>
 <script>
     function mostarmodificarm(id, medidor, fecha){
         
         $("#idm").val(id);
         $("#fecham").val(fecha);
           var form_data={
            existente:13,
            uvms:id,
            medidor:medidor
        };

        $.ajax({
            type: 'POST',
            url: "consultasBasicas/consultarNumeros.php",
            data:form_data,
            success: function (data) { 
                
                $("#medidorm").html(data).fadeIn();
                $("#medidorm").val(medidor);
                $("#myModalModificarMedidor").modal('show');
                
            }
        });
        
     }
 </script>
<script>
    function modificarMedidor(){
            var id= document.getElementById('idm').value;
            var medidor = document.getElementById('medidorm').value;
            var fecha = document.getElementById('fecham').value;
      
              $.ajax({
        type:"GET",
        url:"json/modificar_GP_UV_MEDIDOR_SJson.php?id="+id+"&medidor="+medidor+"&fecha="+fecha,
        success: function (data) {
          result = JSON.parse(data);
          if(result=='1'){
                $("#myModal11").modal('show');
                $("#ver11").click(function(){
                    document.location = 'GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo $id;?>';
                });
              }else{
              if(result=='2'){
                $("#myModal13").modal('show');
                $("#ver13").click(function(){
                    document.location = 'GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo $id;?>';
                });
                } else {
                $("#myModal12").modal('show');
                $("#ver12").click(function(){
                    document.location = 'GP_UNIDAD_VIVIENDA_SERVICIO.php?id=<?php echo $id;?>';
                });
                }
              }
        }
      });
          }
    </script>
</body>
</html>


