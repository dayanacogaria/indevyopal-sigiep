<?php 
#13/03/207 --- Nestor B --- se modificó la función stetoupper por mb_strtolower
require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
 $id = $_GET["id"];
 
 $queryCond = "SELECT pn.id_unico,pn.nombre,pn.tipo_periodo_nomina,pne.nombre FROM gn_tipo_proceso_nomina pn
 LEFT JOIN gn_tipo_periodo_nomina_MQ  pne ON pn.tipo_periodo_nomina=pne.id_unico 
 WHERE md5(pn.id_unico) = '$id'"; 


 $resul = $mysqli->query($queryCond);
 $row = mysqli_fetch_row($resul);
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once './head.php';
?>

<title>Modificar Tipo Proceso Nomina</title>
<link href="css/select/select2.min.css" rel="stylesheet">

    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                  
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar  Tipo Proceso Nomina</h2>
                      
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipoProcesoNominaJson.php">
                          
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                             <div class="form-group" style="margin-top: -10px;">
                                 <label for="nombre" class="col-sm-5 control-label"><strong class="obligado">*</strong>Nombre:</label>
                                 <input type="text" name="nombre" value="<?php echo ucwords((mb_strtolower($row[1]))) ?>" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
                            </div>   
                            <div class="form-group" style="margin-top: -10px;">     
                                 <?php
                                $tipoP   = "SELECT id_unico, nombre FROM gn_tipo_periodo_nomina_MQ";
                                
                                $tipPN = $mysqli->query($tipoP);
                                ?>
                                <div class="form-group" style="margin-top: -5px">
                                    <label class="control-label col-sm-5">
                                            <strong class="obligado"></strong>Tipo Periodo Nómina Electrónica:
                                    </label>
                                    <select name="sltNominaE" class="select2_single form-control" id="sltNominaE" title="Seleccione el Tipo de Periodo Nómina Electrónica" style="height: 30px">
                                    <?php
                                    if(!empty($row[2])){
                                    ?>
                                    <option value="<?php echo $row[2]?>"><?php echo $row[3]?></option>
                                    <?php
                                    }else{
                                    ?>
                                     <option value="">-</option>
                                        <?php
                                    }
                                        while ($filaTp = mysqli_fetch_row($tipPN)) { ?>
                                        <option value="<?php echo $filaTp[0];?>"><?php echo $filaTp[1];?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            
                                </div>                                    
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
    <script src="js/select/select2.full.js"></script>
    <script>
         $(document).ready(function() {
         $(".select2_single").select2({

        allowClear: true
      });


    });
    </script>
</html>