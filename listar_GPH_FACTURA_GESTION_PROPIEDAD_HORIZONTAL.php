<?php
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes 
require_once 'head_listar.php';
require_once('Conexion/conexion.php');
$anno = $_SESSION['anno'];
  $queryTipoC = "SELECT 
                f.id_unico,
                f.numero_factura,
                (SELECT CONCAT_WS(' ',ta.nombreuno,ta.nombredos,ta.apellidouno,ta.apellidodos) FROM
                gph_espacio_habitable_tercero eht   
                left join gf_tercero ta ON ta.id_unico=eht.id_tercero    
                WHERE ta.id_unico = eht.id_tercero 
                and eht.id_espacio_habitable = f.id_espacio_habitable and eht.principal='2'
                ORDER BY ta.id_unico ASC LIMIT 0,1) AS nom_propietario,
              (SELECT ta.razonsocial from  gph_espacio_habitable_tercero eht   
                left join gf_tercero ta ON ta.id_unico=eht.id_tercero    
                WHERE ta.id_unico = eht.id_tercero  
                and eht.id_espacio_habitable = f.id_espacio_habitable and eht.principal='2'
                ORDER BY ta.id_unico ASC LIMIT 0,1) AS razon_social_propietario,                
                CONCAT_WS(' - ',eh.codigo,eh.descripcion) as apto,
                f.descripcion,
                DATE_FORMAT(f.fecha_factura,'%d/%m/%Y'),
                DATE_FORMAT(f.fecha_vencimiento,'%d/%m/%Y'),
                tf.nombre as tipo_factura,
                 (select sum(df.valor+df.iva+df.ajuste_peso+df.impoconsumo) as vlr from gp_detalle_factura df 
                where df.factura=f.id_unico) as vl_factura,
                
                (IF(t.razonsocial IS NULL OR t.razonsocial ='', CONCAT_WS(' ',t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos), t.razonsocial)), 
                t.numeroidentificacion
                
                FROM gp_factura as f 
                left join gp_tipo_factura tf on tf.id_unico=f.tipofactura
                left join gf_centro_costo cc on cc.id_unico=f.centrocosto
                left join gp_estado_factura ef on ef.id_unico=f.estado_factura
                left join gh_espacios_habitables eh on eh.id_unico=f.id_espacio_habitable
                LEFT JOIN gf_tercero t ON f.tercero = t.id_unico 
                WHERE f.parametrizacionanno = $anno 
                    AND tf.id_unico != 1 
           order by f.fecha_factura  desc";
  $resultado = $mysqli->query($queryTipoC);
?>
    <title>Listar Facturas</title>
  </head>
<body>  
<div class="container-fluid text-center">
  <div class="row content">

  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Facturación</h2>
      <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
          <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
            <thead>

              <tr>
                <td style="display: none;">Identificador</td>
                <td width="30px" align="center"></td>
                <td><strong>Tipo Factura</strong></td>
                <td><strong>Nº Factura</strong></td>
                <td><strong>Propietario</strong></td>
                <td><strong>Espacio Habitable</strong></td>
                <td><strong>Descripción</strong></td>
                <td><strong>Fecha Factura</strong></td>
                <td><strong>Fecha Vencimiento</strong></td>                
                <td><strong>Valor Factura</strong></td>
                
              </tr>

              <tr>
                <th style="display: none;">Identificador</th>
                <th width="7%"></th>
                <th>Tipo Factura</th>   
                <th>Nº Factura</th>
                <th>Propietario</th>            
                <th>Espacio Habitable</th>            
                <th>Descripción</th>            
                <th>Fecha Factura</th>            
                <th>Fecha Vencimiento</th>                                     
                <th>Valor Factura</th>            
              </tr>

            </thead>
            <tbody>
              
              <?php
                while($row = mysqli_fetch_row($resultado)){
                    $tercer=$row[3];
                    if(empty($tercer)){
                        $tercer=$row[2];
                    }else{
                        $tercer=$row[3];
                    }
                    ?>
               <tr>
                <td style="display: none;"></td>
                <td>
                    <a class="campos" href="ver_GPH_FACTURA_GESTION_PROPIEDAD_HORIZONTAL.php?id=<?php echo md5($row[0]);?>">
                    <i title="Ver Detalle" class="glyphicon glyphicon-eye-open" ></i>
                 </a>
                </td>
                <td><?php echo    ucwords(mb_strtolower($row[8]))?></td>      
                <td><?php echo    ucwords(mb_strtolower($row[1]))?></td>      
                <td><?php echo    ucwords(mb_strtolower($row[10])).' - '.$row[11]?></td>      
                <td><?php echo    ucwords(mb_strtolower($row[4]))?></td>      
                <td><?php echo    ucwords(mb_strtolower($row[5]))?></td>      
                <td><?php echo    ucwords(mb_strtolower($row[6]))?></td>      
                <td><?php echo    ucwords(mb_strtolower($row[7]))?></td>      
                
                <td><?php echo    number_format($row[9], 2, '.', ',')?></td>      
              </tr>
              <?php } ?>

            </tbody>
          </table>

              <div align="right"><a href="registrar_GPH_FACTURA_GESTION_PROPIEDAD_HORIZONTAL.php" class="btn btn-primary sombra" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: 20px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> </div>       

        </div>      
      </div>
    </div>
  </div>
</div>


</body>
</html>
