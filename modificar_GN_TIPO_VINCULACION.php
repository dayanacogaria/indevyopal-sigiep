<?php 
#09/03/2017 --- Nestor B --- se modificó la función strtoupper por mb_strtolower para que al traer la información me convierta la cadena en minuscula
require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
 $id = $_GET["id"];
 
 $queryCond = "SELECT tvp.id_unico, tvp.nombre,tvp.tipo_vinculacion,tp.nombre
 FROM gn_tipo_vinculacion   tvp
 LEFT JOIN tipo_vinculacion tp ON tp.id_unico=tvp.tipo_vinculacion
    WHERE md5(tvp.id_unico) = '$id'"; 

 $resul = $mysqli->query($queryCond);
 $row = mysqli_fetch_row($resul);
  $idvin=$row[2];
 $nombrevin=$row[3];
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once './head.php';
?>

<title>Modificar Tipo Vinculación</title>

    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                  
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar  Tipo Vinculación</h2>
                      
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipoVinculacionJson.php">
                          
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                             <div class="form-group" style="margin-top: -10px;">
                                 <label for="nombre" class="col-sm-5 control-label"><strong class="obligado">*</strong>Nombre:</label>
                                 <input type="text" name="nombre" value="<?php echo ucwords((mb_strtolower($row[1]))) ?>" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
                            </div> 
                              <!------------------------- Consulta para llenar Código CGR-->
                        <?php 
                        
                            $vinculacion = "SELECT id_unico, nombre FROM tipo_vinculacion";
                            $codigo = $mysqli->query($vinculacion);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Tipo Vinculacion:
                            </label>
                            <select name="sltVincu" class="form-control" id="sltVincu" title="Seleccione código CGR" style="height: 30px">
                            <?php if($idvin==""){?>
                             <option value="">Seleccione el tipo vinculacion</option>
                                <?php 
                            }else{?>
                            <option value="<?php echo $idvin?>"><?php echo $idvin.'-'.$nombrevin?></option>
                           <?php  
                            }?> 
                                <?php 
                                while ($filaC = mysqli_fetch_row($codigo)) { ?>
                                <option value="<?php echo $filaC[0];?>"><?php echo $filaC[0].'-'.$filaC[1]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""> </option>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Código CGR-->                                        
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