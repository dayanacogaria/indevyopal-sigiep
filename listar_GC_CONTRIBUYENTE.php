<?php


require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';


/* 14/01/2019|LORENA M.|Valida si el índice está definido la primera vez que se carga el formulario y asigna el valor del estado 1 para activos cuando se carga el formulario y 2 para inactivos cuando se hace clic en el botón: Mostrar inactivos y cuando se hace nuevamente clic en el botón filtra por activos */ 

$estini = 2;
$nomest = "Mostrar Inactivos/Suspendidos";
$estado=(isset($_GET['id']))?$_GET['id']:"";

if ($estado == 2) {
  $string = " WHERE c.estado in (2,20) ";
  $estini = 1;
  $nomest = "Mostrar Activos/Otros";
} else{
  $string = " WHERE c.estado = 1 ";
}
                                     
$sql = "SELECT c.id_unico,c.codigo_mat,c.codigo_mat_ant,
                                IF(CONCAT_WS(' ',
                                t.nombreuno,
                                t.nombredos,
                                t.apellidouno,
                                t.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                t.nombreuno,
                                t.nombredos,
                                t.apellidouno,
                                t.apellidodos) = '',
                                (t.razonsocial),
                                CONCAT_WS(' ',
                                t.nombreuno,
                                t.nombredos,
                                t.apellidouno,
                                t.apellidodos)) AS NOMBRETERCERO ,
                                c.cod_postal,
                                c.repre_legal,

                                IF(CONCAT_WS(' ',
                                ter.nombreuno,
                                ter.nombredos,
                                ter.apellidouno,
                                ter.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                ter.nombreuno,
                                ter.nombredos,
                                ter.apellidouno,
                                ter.apellidodos) = '',
                                (ter.razonsocial),
                                CONCAT_WS(' ',
                                ter.nombreuno,
                                ter.nombredos,
                                ter.apellidouno,
                                ter.apellidodos)) AS nombreRL ,
                                ec.nombre,
                                c.fechainscripcion,
                                c.dir_correspondencia,
                                t.numeroidentificacion
                            

        FROM gc_contribuyente c 
        LEFT JOIN gc_estado_contribuyente ec ON c.estado = ec.id_unico
        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
        LEFT JOIN gf_tercero ter ON ter.id_unico=c.repre_legal ". $string." ";
             
    
$resultado  = $mysqli->query($sql);


?>   
 <style>
        .btn-g{
           padding: 1px 6px !important; 
           color: #000000d6 !important;
         }
        .btn-g:hover
            {            
            background-color:#00548f;
            color: #ffff !important;
        }
        .btn-e{
           padding: 1px 6px !important; 
           color: red !important;
         }
        .btn-e:hover
            {            
            background-color:#00548f;
            color: #ffff !important;
        }


        </style>
     <title>Listar Contribuyente</title>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Contribuyentes</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        <td class="cabeza"><strong>Estado</strong></td>
                                        <td class="cabeza"><strong>Código Matrícula Actual</strong></td>
                                        <td class="cabeza"><strong>Código Matrícula Anterior</strong></td>
                                        <td class="cabeza"><strong>Identificación</strong></td>
                                        <td class="cabeza"><strong>Tercero</strong></td>    
                                        <!--<td class="cabeza"><strong>Código Postal</strong></td> -->   
                                        <td class="cabeza"><strong>Representante Legal</strong></td> 
                                        <td class="cabeza"><strong>Dirección</strong></td> 
                                        <td class="cabeza"><strong>Fecha Inscripción</strong></td>       
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th class="cabeza">Estado</th>
                                        <th class="cabeza">Código Matrícula Actual</th>
                                        <th class="cabeza">Código Matrícula Anterior</th>
                                        <th class="cabeza">Identificación</th>
                                        <th class="cabeza">Tercero</th>
                                        <!--<th class="cabeza">Código Postal</th>-->
                                        <th class="cabeza">Representante Legal</th>
                                        <th class="cabeza">Dirección</th>
                                        <th class="cabeza">Fecha Inscripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) {
                                      
                                        if(!empty($row[8])){
                                            $fecha_div =  explode("-", $row[8]);
                                            $anio1 = $fecha_div[0];
                                            $mes1 = $fecha_div[1];
                                            $dia1 = $fecha_div[2];
                                            $fecha = ''.$dia1.'/'.$mes1.'/'.$anio1.''; 
                                        }else{
                                            $fecha = "";
                                        }
                                        
                                    ?>
                                     <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td class="campos" >
                                          
                                            <a type="button" class="btn-g campos" href="modificar_GC_CONTRIBUYENTE.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                              <a type="button" class="btn-e campos"  href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash" ></i>
                                            </a>
                                        </td>
                                        <td class="campos"><?php echo $row[7]?></td> 
                                        <td class="campos" align="right"><?php echo $row[1]?></td>                
                                        <td class="campos" align="right"><?php echo $row[2]?></td>  
                                        <td class="campos"><?php echo $row[10]?></td>                           
                                       <td class="campos"><?php echo ucwords(mb_strtolower($row[3])) ?></td> 
                                        <!--<td class="campos"><?php echo $row[4]?></td> -->
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[6]))?></td>       
                                        <td class="campos"><?php echo ucwords(mb_strtolower($row[9]))?></td>       
                                        <td class="campos"><?php echo $fecha?></td> 

                                    </tr>                                    
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                           
                              
                              <!-- 08/01/2019|LORENA M.| Creación del botón para filtrar inactivos y activos !-->
                             <div class="row" style="margin-right: 0px !important;"> 
                             <div class="col-xs-6 col-md-6 col-sm-6" >  
                              <input type="button" value="<?php echo $nomest ?>" name="inact" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:20px; margin-right:40px;" 
                              onclick="window.location='listar_GC_CONTRIBUYENTE.php?id=<?php echo $estini ?>';"  > 
                            </div >  
                            <div class="col-xs-6 col-md-6 col-sm-6" align="right" > 
                              <a href="registrar_GC_CONTRIBUYENTE.php" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:20px">Registrar Nuevo</a>
                                  
                            </div>
                          </div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once './footer.php'; ?>
   <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Contribuyente?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
          <p>Información eliminada correctamente.</p>
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
          <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>


  <!--Script que dan estilo al formulario-->

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
<!--Scrip que envia los datos para la eliminación-->
    
<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"jsonComercio/eliminarContribuyenteJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true)
                      $("#myModal1").modal('show');
                 else
                      $("#myModal2").modal('show');
                  }
              });
          });
      }
  </script>

  <script type="text/javascript">
      function modal()
      {
         $("#myModal").modal('show');
      }
  </script>
    <!--Actualiza la página-->
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'listar_GC_CONTRIBUYENTE.php';
      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'listar_GC_CONTRIBUYENTE.php';
      });    
  </script>
    </body>
</html>