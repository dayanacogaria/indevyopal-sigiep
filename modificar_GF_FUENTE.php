<?php
###########################################################################################################
#                           MODIFICACIONES
#05/07/2017 |ERICA G. | PARAMETRIZACION                            
#10/04/2017 | Erica G. | Diseño, tíldes, búsquedas 
###########################################################################################################

require_once 'head.php'; 
require_once('Conexion/conexion.php');
$id = " ";
$param=$_SESSION['anno'];
//validacion preguntando si la variable enviada del listar viene vacia
if (isset($_GET["id"]))
{ 
  $id_fuente = (($_GET["id"]));
//Query o sql de consulta
  $queryfuente = "SELECT f.id_unico, f.nombre, 
      f.movimiento, f.predecesor,  fu.nombre, f.tipofuente, tf.nombre, f.recursofinanciero, 
      rf.nombre, f.equivalente    
        FROM gf_fuente f  
        LEFT JOIN  gf_fuente fu ON f.predecesor=fu.Id_Unico 
        LEFT JOIN gf_tipo_fuente tf ON f.tipofuente=tf.Id_Unico
        LEFT JOIN gf_recurso_financiero rf ON f.recursofinanciero=rf.id_unico
        WHERE md5(f.Id_Unico) = '$id_fuente'";
}
/*Variable y proceso en el que se llama de manera embebida con la conexión el cual pérmite realizar el proceso de consulta*/
  $resultado = $mysqli->query($queryfuente);
  $row = mysqli_fetch_row($resultado);

//consultas para llenar los campos
  $tipo = "SELECT Id_Unico, Nombre FROM gf_tipo_fuente  WHERE id_unico != '$row[5]'ORDER BY Nombre ASC";
  $tipoF =   $mysqli->query($tipo);

  $recurso = "SELECT Id_Unico, Nombre FROM gf_recurso_financiero WHERE id_unico != '$row[7]'  AND parametrizacionanno = $param ORDER BY Nombre ASC";
  $recursoF = $mysqli->query( $recurso);

  //Lleno
  $predecesor = "SELECT id_unico, nombre FROM gf_fuente WHERE id_unico != '$row[0]' AND id_unico != '$row[3]'   AND parametrizacionanno = $param ORDER BY nombre ASC";
  $prede = $mysqli->query($predecesor);
  
  //vacio
  $predecesorV= "SELECT id_unico, nombre FROM gf_fuente WHERE id_unico != '$row[0]'  AND parametrizacionanno = $param ORDER BY id_unico ASC";
  $predeV = $mysqli->query($predecesorV);
  

?>

<!-- Llamado a la cabecera del formulario -->

<link href="css/select/select2.min.css" rel="stylesheet">
<title>Modificar Fuente</title>
</head>
<!-- contenedor principal -->  
<div class="container-fluid text-center">
    <div class="row content">
        <!-- Llamado al menú del formulario -->  
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-7 text-left" style="margin-top:0px">
            <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: 4px;">Modificar Fuente</h2>
            <a href="listar_GF_FUENTE.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Fuente: <?php echo ucwords(mb_strtolower($row[1]))?></h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <!-- Inicio del formulario -->
                <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarFuenteJson.php">
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'num_car')" placeholder="Nombre" value="<?php echo ucwords(mb_strtolower($row[1])); ?>" required>
                    </div>
                    <div class="form-group" style="margin-top: -10px;">
                        <label for="mov" class="col-sm-5 control-label" style="margin-top:-5px;"><strong style="color:#03C1FB;">*</strong>Movimiento:</label>
                        <?php switch ($row[2]) {
                          case 1: ?>
                            <input type="radio" name="mov" id="mov"  value="1" checked>SI
                            <input type="radio" name="mov" id="mov" value="2" >NO                  
                          <?php
                            break;
                          case 2: ?>
                          <input type="radio" name="mov" id="mov"  value="1" >SI
                          <input type="radio" name="mov" id="mov" value="2" checked>NO
                        <?php
                            break;
                        } ?>
                    </div>
                    <div class="form-group" style="margin-top: 25px;">
                        <label for="prede" class="col-sm-5 control-label">Predecesor:</label>
                        <select name="prede" id="prede" class="select2_single form-control" title="Seleccione predecesor">
                            <?php if (empty($row[3])) { ?>
                            <option value="">-</option>
                            <?php while ($row1 = mysqli_fetch_row($predeV)) { ?>
                            <option value="<?php echo $row1[0]?>"><?php echo ucwords(mb_strtolower($row1[0].' - '.$row1[1]))?></option>        
                            <?php }?>
                            <?php } else { ?>
                            <option value="<?php echo $row[3]?>"><?php echo ucwords(mb_strtolower($row[4]))?></option>
                            <?php while ($row2 = mysqli_fetch_row($prede)) { ?>
                            <option value="<?php echo $row2[0]?>"><?php echo ucwords(mb_strtolower($row2[0].' - '.$row2[1]))?></option>
                            <?php }?>
                            <option value="">-</option>
                            <?php } ?>
                        </select> 
                    </div>
                    <div class="form-group">
                        <label for="tipoF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Fuente:</label>
                        <select name="tipoF" id="tipoF" class="select2_single form-control" title="Seleccione Tipo Fuente" required>
                           <?php if(empty($row[5])) { ?>
                            <option value="">-</option>
                           <?php } else { ?> 
                           <option value="<?php echo $row[5]?>"><?php echo ucwords(mb_strtolower($row[6]));?></option>
                           <option value="">-</option>
                           <?php } ?>
                          <?php while($rowC = mysqli_fetch_assoc($tipoF)){?>
                          <option value="<?php echo $rowC['Id_Unico'] ?>"><?php echo ucwords((mb_strtolower($rowC['Nombre'])));}?></option>
                        </select> 
                    </div>
                    <div class="form-group">
                        <label for="recurso" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Recurso Financiero:</label>
                        <select name="recurso" id="recurso" class="select2_single form-control" title="Seleccione recurso financiero" required>
                           <?php if(empty($row[7])) { ?>
                            <option value="">-</option>
                           <?php } else { ?>
                            <option value="<?php echo $row[7]?>"><?php echo ucwords(mb_strtolower($row[8]));?></option>
                            <option value="">-</option>
                           <?php } ?>
                          <?php while($rowR = mysqli_fetch_assoc($recursoF)){?>
                          <option value="<?php echo $rowR['Id_Unico'] ?>"><?php echo ucwords((mb_strtolower($rowR['Nombre'])));}?></option>
                        </select> 
                    </div>
                    <div class="form-group" >
                        <label class="control-label col-sm-5">
                            Equivalente:
                        </label>
                        <input class="form-control" placeholder="Equivalente" type="text" name="equivalente" id="equivalente" title="Ingrese el código equivalente" onkeypress="return txtValida(event, 'num')" value="<?php echo $row[9]?>">
                    </div>
                    <div align="center">
                        <button type="submit" class="btn btn-primary sombra" style="margin-left: -50px">Guardar</button>
                    </div>
                    <input type="hidden" name="MM_insert" >
                </form>
            <!-- Fin de división y contenedor del formulario -->          
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
                      <div class="btnConsultas" style="margin-bottom: 1px;"><a href="#">MOVIMIENTO <br/>PRESUPUESTAL</a></div>
                    </td>
                    <td>
                        <a href="registrar_GF_TIPO_FUENTE.php" class="btn btn-primary btnInfo">TIPO FUENTE</a>
                    </td>
                  </tr>
                  <tr>
                    <td align="center">
                      <div class="btnConsultas" style="margin-bottom: 1px;"><a href="#"> <br/>RESUMEN</a></div>
                    </td>
                    <td>
                        <a href="registrar_GF_RECURSO_FINANCIERO.php" class="btn btn-primary btnInfo">RECURSO FINANCIERO</a>
                    </td>
                  </tr>
                  <tr>
                    <td align="center">
                      <div class="btnConsultas" style="margin-bottom: 1px;"><a href="#"> <br/>GRAFICOS</a></div>
                    </td>
                    <td></td>
                  </tr>
                </tbody>
            </table>                
        </div>
    </div>
    <!-- Fin del Contenedor principal -->  
</div>
<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php'; ?>
<script src="js/select/select2.full.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<!-- select2 -->
 

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>
</body>
</html>
