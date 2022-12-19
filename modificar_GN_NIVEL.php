<?php require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT    n.id_unico,
                    n.nombre,
                    n.equivalentesia,
                    es.id_unico,
                    es.nombre,
                    n.codigocgr,
                    cc.id_unico,
                    cc.nombre,
                    n.estadonivel,
                    en.id_unico,
                    en.nombre,
                    n.equivalente_sui,
                    cp.nombre,
                    n.codigoPersonal
                FROM gn_nivel n
                LEFT JOIN   gn_equivalente_sia es   ON n.equivalentesia = es.id_unico
                LEFT JOIN   gn_codigo_cgr cc        ON n.codigocgr = cc.id_unico
                LEFT JOIN   gn_estado_nivel en      ON n.estadonivel = en.id_unico
                LEFT JOIN codigo_personal cp ON cp.id_unico=n.codigoPersonal
                where md5(n.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
$nid    = $row[0];
$nnom   = $row[1];
$neqs   = $row[2];
$eqid   = $row[3];
$eqnom  = $row[4];
$ncgr   = $row[12];
$ncid   = $row[6];
$ncnom  = $row[7];
$nen    = $row[8];
$enid   = $row[9];
$enom   = $row[10];
$equiSui   = $row[11];
$ncgrid   = $row[13];

/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>
<title>Modificar Nivel</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Nivel</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarNivelJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                             
<!-----Campo para llenar Nombre---->                              
                                <div class="form-group" style="margin-top: -10px;">
                                 <label for="nombre" class="col-sm-5 control-label"><strong class="obligado"></strong>Nombre:</label>
                                <input type="text" name="txtNombre" value="<?php echo $nnom?>" id="txtNombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre">
                            </div>  
<!--Fin Campo para llenar Nombre---->
<!------------------------- Consulta para llenar Equivalente SIA-->
                        <?php 
                        if($neqs=="")
                        $sia = "SELECT id_unico, nombre FROM gn_equivalente_sia";
                            else
                        $sia = "SELECT id_unico, nombre FROM gn_equivalente_sia  where id_unico != $neqs";
                        $equivalente = $mysqli->query($sia);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Equivalente SIA:
                            </label>
                            <select name="sltEquivalentesia" class="form-control" id="sltEquivalentesia" title="Seleccione equivalente sia" style="height: 30px">
                            <option value="<?php echo $eqid?>"><?php echo $eqnom?></option>
                                <?php 
                                while ($filaE = mysqli_fetch_row($equivalente)) { ?>
                                <option value="<?php echo $filaE[0];?>"><?php echo $filaE[1]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""> </option>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Equivalente SIA-->
<!------------------------- Consulta para llenar Código CGR-->
                        <?php 
                        
                            $cgr = "SELECT id_unico, nombre FROM codigo_personal";
                        $codigo = $mysqli->query($cgr);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Código CGR:
                            </label>
                            <select name="sltCodigocgr" class="form-control" id="sltCodigocgr" title="Seleccione código CGR" style="height: 30px">
                            <?php if($ncgr==""){?>
                             <option value="">Selecciones el codigo CGR</option>
                                <?php 
                            }else{?>
                            <option value="<?php echo $ncgrid?>"><?php echo $ncgrid.'-'.$ncgr?></option>
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
<!------------------------- Consulta para llenar Estado Nivel-->
                        <?php
                        if($nen=="")
                            $niv = "SELECT id_unico, nombre FROM gn_estado_nivel";
                        else  
                            $niv = "SELECT id_unico, nombre FROM gn_estado_nivel where id_unico != $nen";
                        $estado = $mysqli->query($niv);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Estado Nivel:
                            </label>
                            <select name="sltEstadonivel" class="form-control" id="sltEstadonivel" title="Seleccione estado nivel" style="height: 30px">
                            <option value="<?php echo $enid?>"><?php echo $enom?></option>
                                <?php 
                                while ($filaEN = mysqli_fetch_row($estado)) { ?>
                                <option value="<?php echo $filaEN[0];?>"><?php echo $filaEN[1]; ?></option>
                                <?php
                                }
                                ?>
                                <option value=""> </option>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Estado Nivel--> 
<!------------------------- Consulta para llenar Equivalente SUI-->
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="equivalenteSui" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Equivalente SUI:</label>
                                <select name="equivalenteSui" class="select2_single form-control" id="equivalenteSui" title="Seleccione Equivalente SUI" style="height: 30px">
                                <?php if(!empty($equiSui)){ ?>
                                        <option value="<?php echo $equiSui?>"><?php echo $equiSui;?></option>
                                    <?php }else{ ?>
                                        <option value="">-</option>
                                    <?php }?>    
                                    <option value="">Seleccione Equivalente Sui</option>    
                                    <option value="Personal Directivo">Personal Directivo</option>
                                    <option value="Personal Administrativo">Personal Administrativo</option>
                                    <option value="Personal Tecnico - Operativo">Personal Tecnico - Operativo</option>
                                    <option value="">-</option>
                                  
                                </select>
                            </div>                                               
<!----------Fin Consulta Para llenar Equivalente SUI-->                                
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
