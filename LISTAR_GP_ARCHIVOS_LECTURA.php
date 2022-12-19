<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');

 $query = "SELECT
  id_unico, fecha, nombre, descripcion, ruta FROM gp_lectura_archivos"; 

$resultado = $mysqli->query($query);


?>
  <title>Listar Archivos Lectura</title>
</head>
<body>

<div class="container-fluid text-center">
  <div class="row content">
    
    <?php require_once ('menu.php'); ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Archivos Lectura</h2>

      

      <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>

              <tr>
                <td style="display: none;">Identificador</td>
                <td width="30px" align="center" style="display: none;"></td>
                <td><strong>Fecha/Hora</strong></td>
                <td><strong>Nombre</strong></td>
                <td><strong>Descripción</strong></td>
                <td><strong>Ver</strong></td>
                
                
              </tr>

              <tr>
                <th style="display: none;">Identificador</th>
                <th style="display: none;" width="7%"></th>
                <th>Fecha/Hora</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Ver</th>                
              </tr>

            </thead>
            <tbody>
              
              <?php
                while($row = mysqli_fetch_row($resultado)){?>
               <tr>
                <td style="display: none;"><?php echo $row[0]?></td>
                <td style="display: none;"><?php echo $row[0]?></td>
                <td><?php echo date("d/m/Y H:i:s", strtotime($row[1]));?></td>
                <td><?php echo ucwords(strtolower(($row[2])));?></td>
                <td><?php echo ucwords(strtolower(($row[3])));?></td>
                <td><a href="<?php echo $row[4]?>" ><i title="ver" class="glyphicon glyphicon-search"></i></a></td>
                
               </tr>
              <?php } ?>


            </tbody>
          </table>
        </div>
        
       
      </div>
      
    </div>

  </div>
</div>
  <?php require_once ('footer.php'); ?>


</body>
</html>


