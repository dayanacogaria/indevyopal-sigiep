<?php
require_once('Conexion/conexion.php');
require_once 'head_listar.php';

$nombre = $_SESSION['tabla_creada'];

$queryCargo = "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY, EXTRA 
	FROM INFORMATION_SCHEMA.COLUMNS 
	WHERE table_name = '$nombre'";
$resultado = $mysqli->query($queryCargo);

?>
  <title>Tabla Creada</title>
  <style type="text/css">
    .area { height: auto !important;}  
    table.dataTable thead th,table.dataTable thead td {padding: 1px 18px;font-size: 10px;}
    table.dataTable tbody td,table.dataTable tbody td {padding: 1px;}
    .dataTables_wrapper .ui-toolbar {padding: 2px;font-size: 10px;}
    .control-label { font-size: 12px;}
    .itemListado{margin-left:5px;margin-top:5px;width:150px;cursor:pointer;}
    #listado {width:150px;height:80px;overflow: auto;background-color: white;}
  </style>
</head>
<body>
  <div class="container-fluid text-center">
    <div class="row content">
      <?php require_once('menu.php'); ?>
        <div class="col-sm-10 text-left">
          <h2 id="forma-titulo3" align="center" style="margin-bottom: 15px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Tabla <?php echo $nombre; ?> </h2>
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
              <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <td style="display: none;">Identificador</td>
                    <td width="30px" align="center"></td>
                    <td><strong>Nombre</strong></td> 
                    <td><strong>Tipo</strong></td> 
                    <td><strong>Nulo</strong></td> 
                    <td><strong>Llave</strong></td> 
                    <td><strong>Nombre</strong></td> 
                  </tr>
                  <tr>
                    <th style="display: none;">Identificador</th>
                    <th width="7%"></th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Nulo</th>
                    <th>Llave</th>
                    <th>Nombre</th>
                  </tr>
                </thead>
                <tbody>
                <?php while($row = mysqli_fetch_row($resultado)) { ?>
                  <tr>
                    <td style="display: none;"></td>
                    <td></td>
                    <td><?php echo ucwords(strtolower($row[0]));?></td>
                  	<td><?php echo ucwords(strtolower($row[1]));?></td>
                  	<td><?php echo ucwords(strtolower($row[2]));?></td>
                  	<td><?php echo ucwords(strtolower($row[3]));?></td>
                  	<td><?php echo ucwords(strtolower($row[4]));?></td>
                  </tr>
              <?php } ?>
              </tbody>
            </table>            
          </div>
          <div class="col-sm-offset-11 text-right" style="margin-top: 5px">
            <a class="btn btn-primary" title="Cargar Archivo" onclick="window.open('cargar_archivo.php?table=<?php echo ($nombre) ?>')"><li class="glyphicon glyphicon-open-file"></li></a>
          </div>  
        </div>
      </div>
    </div>
  </div>
  <?php require_once 'footer.php'; ?>
</body>
</html>

