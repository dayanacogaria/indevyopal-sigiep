<?php require_once('Conexion/conexion.php');
 session_start();
 $id = $_GET["id"];
 $queryCond = "SELECT TS.id_unico, 
                      TS.nombre, 
                      TS.tipo_medicion,
                      TM.id_unico,
                      TM.nombre
                      FROM gp_tipo_servicio TS
                      LEFT JOIN gp_tipo_medicion TM
                      ON TS.tipo_medicion = TM.id_unico
                      WHERE md5(id_unico) = '$id'"; 

 $resultado = $mysqli->query($queryCond);
 $row = mysqli_fetch_row($resultado);
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
$mediciones = "SELECT id_unico, nombre FROM gp_tipo_medicion WHERE id_unico != $row[2] ORDER BY Nombre ASC";
$medicion =   $mysqli->query($mediciones);
 
require_once './head.php';
?>
<title>Modificar Tipo Servicio</title>
    </head>
    <body>
    <div class="container-fluid text-center">
      <div class="row content">
       <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left">
         <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Tipo Servicio</h2>
          <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarTipoServicioJson.php">
           <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.
           </p>               
           <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
           </div>
           <div class="form-group">
            <?php 
              $sql = "SELECT nombre,id_unico FROM gp_tipo_medicion ORDER BY nombre ASC";
              $rs = $mysqli->query($sql);
            ?>
            <label for="TipoMedicion" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Medición:</label>
             <select name="sltMedicion" id="sltMedicion" class="form-control" title=
                      "Seleccione Medición" style="height: 30px">
             <option value="">Medición</option>
             <?php 
                 while ($fila = mysqli_fetch_row($rs)) 
                 { 
             ?>
             <option value="<?php echo $fila[1];?>"><?php echo ucwords(($fila[0]));?></option>                                
             <?php 
             }
             ?>                                    
            </select>
           </div>
           <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
            </div>
            <input type="hidden" name="MM_insert" >
          </form>
         </div>
        </div>                  
      </div>
    </div>
        <?php require_once './footer.php'; ?>
    </body>
</html>
