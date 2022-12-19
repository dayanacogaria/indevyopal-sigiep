<?php require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT                ce.id_unico,
                                c.id_unico,
                                c.codigo,
                                c.descripcion,
                                e.id_unico,
                                e.fechaembargo                                
                FROM		gn_concepto_embargo ce	 
                LEFT JOIN	gn_concepto c ON ce.concepto = c.id_unico
                LEFT JOIN	gn_embargo e ON ce.embargo = e.id_unico
                where md5(ce.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
    $idconc = $row[1];
    $ccod   = $row[2].' - '.$row[3];
    $idemb  = $row[4];
    $fecemb = $row[5];
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>
<title>Modificar Concepto Embargo</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; 
                        

                        ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Concepto Embargo</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarConceptoEmbargoJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
<!------------------------- Consulta para llenar campo Concepto-->
            <?php 
            $sql = "SELECT id_unico, CONCAT(codigo,' - ',descripcion) FROM gn_concepto where id_unico != '$idconc'";
            $concepto = $mysqli->query($sql);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado">*</strong>Concepto:
                </label>
                <select name="sltConcepto" class="form-control" id="sltConcepto" title="Seleccione concepto" style="height: 30px" required="">
                <option value=<?php echo $idconc;?>"><?php echo $ccod?></option>
                    <?php 
                    while ($fila1 = mysqli_fetch_row($concepto)) { ?>
                    <option value="<?php echo $fila1[0];?>"><?php echo $fila1[1]; ?></option>
                    <?php
                    }
                    ?>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar campos Formato-->
                                                           
<!------------------------- Consulta para llenar campo Embargo-->
            <?php 
            $sql = "SELECT id_unico, fechaembargo FROM gn_embargo where id_unico != '$idemb'";
            $embargo = $mysqli->query($sql);
            
//            $fech = $fecemb;
//            $fech = trim($fech, '"');
//            $fecha_div = explode("-", $fech);
//            $aniF = $fecha_div[0];
//            $mesF = $fecha_div[1];
//            $diaF = $fecha_div[2];
//            $fech = $diaF.'/'.$mesF.'/'.$aniF;           
            
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label class="control-label col-sm-5">
                        <strong class="obligado">*</strong>Fecha Embargo:
                </label>
                <select name="sltEmbargo" class="form-control" id="sltEmbargo" title="Seleccione fecha de embargo" style="height: 30px" required="">
                <option value="<?php echo $idemb;?>">
                    <?php 
                                            $fech = $fecemb;
                                            $fech = trim($fech, '"');
                                            $fecha_div = explode("-", $fech);
                                            $anioh = $fecha_div[0];
                                            $mesh = $fecha_div[1];
                                            $diah = $fecha_div[2];
                                            $fech = $diah.'/'.$mesh.'/'.$anioh;
                                            echo $idemb.' - '.$fech;?>
                </option>
                
                    <?php 
                    while ($fila1 = mysqli_fetch_row($embargo)) { ?>
                   
                    <option value="<?php echo $idemb;?>"><?php 
                                            $fec = $fila1[1];
                                            $fec = trim($fec, '"');
                                            $fecha_div = explode("-", $fec);
                                            $aniof = $fecha_div[0];
                                            $mesf = $fecha_div[1];
                                            $diaf = $fecha_div[2];
                                            $fec = $diaf.'/'.$mesf.'/'.$aniof;
                                            echo $fila1[0].' - '.$fec;?></option>
                    <?php
                    }
                    ?>
                </select>   
            </div>
<!------------------------- Fin Consulta para llenar campo Embargo-->
                                                           
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
