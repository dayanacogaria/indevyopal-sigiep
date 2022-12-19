<?php
require_once('Conexion/conexion.php');
require_once("Conexion/ConexionPDO.php");
require_once("./jsonPptal/funcionesPptal.php");
require_once 'head_listar.php';
$con = new ConexionPDO();
$anno = $_SESSION['anno'];

$querytipoC = "SELECT DISTINCT cp.id_unico, cn.id_unico,
        UPPER(tp.codigo), LOWER(tp.nombre),
        cp.numero, DATE_FORMAT(cp.fecha, '%d/%m/%Y')
    FROM gf_comprobante_pptal cp
    LEFT JOIN gf_tipo_comprobante_pptal tp ON cp.tipocomprobante = tp.id_unico
    LEFT JOIN gf_tipo_comprobante tc ON tc.comprobante_pptal = tp.id_unico
    LEFT JOIN gf_comprobante_cnt cn ON tc.id_unico = cn.tipocomprobante AND cp.numero = cn.numero
    LEFT JOIN gf_detalle_comprobante_pptal dcp ON cp.id_unico = dcp.comprobantepptal 
    LEFT JOIN gf_concepto_rubro cr ON dcp.conceptoRubro = cr.id_unico 
    LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
    WHERE tp.clasepptal = 16 AND tp.tipooperacion = 1 AND cn.id_unico IS NOT NULL 
    AND cp.parametrizacionanno = $anno AND c.amortizable = 1"; 
$resultado = $mysqli->query($querytipoC);

#* Año Anterior 
 
$sql = '';
$id_aa = idannoanterior($anno);
if(!empty($id_aa)){
    $sql = $con->Listar("SELECT DISTINCT cp.id_unico, cn.id_unico,
        UPPER(tp.codigo), LOWER(tp.nombre),
        cp.numero, DATE_FORMAT(cp.fecha, '%d/%m/%Y')
    FROM gf_comprobante_pptal cp
    LEFT JOIN gf_tipo_comprobante_pptal tp ON cp.tipocomprobante = tp.id_unico
    LEFT JOIN gf_tipo_comprobante tc ON tc.comprobante_pptal = tp.id_unico
    LEFT JOIN gf_comprobante_cnt cn ON tc.id_unico = cn.tipocomprobante AND cp.numero = cn.numero
    LEFT JOIN gf_detalle_comprobante_pptal dcp ON cp.id_unico = dcp.comprobantepptal 
    LEFT JOIN gf_concepto_rubro cr ON dcp.conceptoRubro = cr.id_unico 
    LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
    LEFT JOIN gf_amortizacion am ON dcp.id_unico = am.detallecomprobantepptal 
    LEFT JOIN gf_detalle_amortizacion da ON am.id_unico = da.amortizacion 
    WHERE tp.clasepptal = 16 AND tp.tipooperacion = 1 AND cn.id_unico IS NOT NULL 
    AND cp.parametrizacionanno = $id_aa AND c.amortizable = 1 AND am.id_unico IS NOT NULL
    AND da.comprobante IS NULL");
            
}

?>
<title>Listar Amortizaciones</title>
</head>
<body>
  
<div class="container-fluid text-center">
  <div class="row content">
    
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:0px">Amortizaciones </h2>
      

      <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>

              <tr>
                <td class="cabeza" style="display: none;">Identificador</td>
                <td class="cabeza" width="30px" align="center"></td>
                <td class="cabeza"><strong>Tipo Comprobante</strong></td>
                <td class="cabeza"><strong>Numero</strong></td>
                <td class="cabeza"><strong>Fecha</strong></td>
                <td class="cabeza"><strong>Concepto</strong></td>
              </tr>


              <tr>
                <th class="cabeza" style="display: none;">Identificador</th>
                <th class="cabeza" width="7%"></th>
                <th class="cabeza">Tipo Comprobante</th>
                <th class="cabeza">Número</th>
                <th class="cabeza">Fecha</th>
                <th class="cabeza">Concepto</th>
              </tr>

            </thead>
            <tbody>
              
              <?php
                while($row = mysqli_fetch_row($resultado)){
                    
                    $sqlcprubro = "SELECT con.id_unico, con.nombre, con.clase_concepto, con.parametrizacionanno, con.amortizable,
                       dpp.valor, dpp.id_unico
                                FROM gf_detalle_comprobante_pptal dpp 
                                LEFT JOIN gf_concepto_rubro cpr ON dpp.conceptoRubro = cpr.id_unico
                                LEFT JOIN gf_concepto con ON cpr.concepto = con.id_unico 
                                WHERE dpp.comprobantepptal = $row[0] AND con.amortizable = 1"; 
                    $rest = $mysqli->query($sqlcprubro);
                    $count = mysqli_num_rows($rest);
                    if ($count > 0){
                    ?>                
                <tr>
                    <td class="campos" style="display: none;"><?php echo $row[0]?></td>
                    <td class="campos"></td>
                    <td class="campos"><?php echo $row[2]." - ".ucwords(mb_strtolower($row[3])); ?></td>
                    <td class="campos"><?php echo $row[4]; ?></td>
                    <td class="campos"><?php echo $row[5]; ?></td>
                    <td class="campos">                        
                    <?php                             
                        while($row2 = mysqli_fetch_row($rest)){ 
                            $sqltz = $con->Listar("
                            SELECT *
                            FROM gf_amortizacion
                            WHERE detallecomprobantepptal = $row2[6];");
                            if ($sqltz[0][0] > 0){                                
                                $amortizacion = md5($sqltz[0][0]);
                                echo "<a href='registrar_GF_AMORTIZACION.php?id=". $amortizacion."' title='Ver'>
                                <li class='glyphicon glyphicon-eye-open'></li>
                                </a>";
                            }else {
                                echo "<a href='registrar_GF_AMORTIZACION.php?concepto=". md5($row2[0])."&detalle=".md5($row2[6])."' title='Generar'>
                                <li class='glyphicon glyphicon-th-list'></li>
                                </a>";                                
                            }                                    
                            echo $row2[1]." $". number_format($row2[5], 2, '.', ',')."<br>";
                        }
                    ?>
                    </td>
                </tr>
                <?php }} 
                for ($i = 0; $i < count($sql); $i++) {
                    $sqlcprubro = "SELECT con.id_unico, con.nombre, con.clase_concepto, con.parametrizacionanno, con.amortizable,
                       dpp.valor, dpp.id_unico
                                FROM gf_detalle_comprobante_pptal dpp 
                                LEFT JOIN gf_concepto_rubro cpr ON dpp.conceptoRubro = cpr.id_unico
                                LEFT JOIN gf_concepto con ON cpr.concepto = con.id_unico 
                                WHERE dpp.comprobantepptal = ".$sql[$i][0]." AND con.amortizable = 1"; 
                    $rest = $mysqli->query($sqlcprubro);
                    $count = mysqli_num_rows($rest);
                    if ($count > 0){ ?>                
                    <tr>
                        <td class="campos" style="display: none;"><?php echo $sql[$i][0]?></td>
                        <td class="campos"></td>
                        <td class="campos"><?php echo $sql[$i][2]." - ".ucwords(mb_strtolower($sql[$i][3])); ?></td>
                        <td class="campos"><?php echo $sql[$i][4]; ?></td>
                        <td class="campos"><?php echo $sql[$i][5]; ?></td>
                        <td class="campos">                        
                    <?php                             
                        while($row2 = mysqli_fetch_row($rest)){ 
                            $sqltz = $con->Listar("
                            SELECT *
                            FROM gf_amortizacion
                            WHERE detallecomprobantepptal = $row2[6];");
                            if ($sqltz[0][0] > 0){                                
                                $amortizacion = md5($sqltz[0][0]);
                                echo "<a href='registrar_GF_AMORTIZACION.php?id=". $amortizacion."' title='Ver'>
                                <li class='glyphicon glyphicon-eye-open'></li>
                                </a>";
                            }else {
                                echo "<a href='registrar_GF_AMORTIZACION.php?concepto=". md5($row2[0])."&detalle=".md5($row2[6])."' title='Generar'>
                                <li class='glyphicon glyphicon-th-list'></li>
                                </a>";                                
                            }                                    
                            echo $row2[1]." $". number_format($row2[5], 2, '.', ',')."<br>";
                        }
                    ?>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
          </table>
              <script>
                  $("#btnamortizacion").click (function(){
                     let id = $(this).data("id");
                    $.post("registrar_GF_AMORTIZACION.php", { id: id }, function (id) {});
                  });
              </script>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Tipo Comprobante Presupuestal?</p>
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
          <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>


  <?php require_once 'footer.php'; ?>

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>

<script type="text/javascript">
      function eliminarTipoC(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarTipoComprobantePptal.php?id="+id,
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
  
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'listar_GF_TIPO_COMPROBANTE_PPTAL.php';
      });
    
  </script>

  <script type="text/javascript">
    
      $('#ver2').click(function(){
        document.location = 'listar_GF_TIPO_COMPROBANTE_PPTAL.php';
      });
    
  </script>

</body>
</html>

