<?php
session_start();
require_once('Conexion/conexion.php');
$imp = 0;
if (empty($_SESSION['usuario']) || empty($_SESSION['compania']) || empty($_SESSION['anno']) || empty($_SESSION['usuario_tercero'])) {
header('Location:index.php');    ?> 
    <script>
        window.location = 'index.php';
    </script>

<?php } else {

    $annoih = $_SESSION['anno'];
    $anh = "SELECT pa.anno, IF(CONCAT_WS(' ',
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
     t.apellidodos)) AS NOMBRE , pb.valor  
    FROM gf_parametrizacion_anno pa 
    LEFT JOIN gf_tercero t ON pa.compania = t.id_unico 
    LEFT JOIN gs_parametros_basicos_sistema pb ON pb.nombre = 'version' 
    WHERE pa.id_unico = $annoih";
    $anh = $mysqli->query($anh);
    $anh = mysqli_fetch_row($anh);
    $ah = $anh[0];
    $ncom = $anh[1];
    if(empty($anh[2])){
        $version = '2020-01';
    } else {
        $version = $anh[2];
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <meta class="viewport" content="width=device-width, initial-scale=1.0, minimun-scalable=1.0"></meta>
            <link rel="icon" href="img/AAA.ico" />
            <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
            <link rel="stylesheet" href="css/style.css">
            <link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="screen" title="default" />
            <script src="js/jquery.min.js"></script>
            <script src="js/jquery-ui.js" type="text/javascript"></script>
            <script type="text/javascript" language="javascript" src="js/jquery-1.10.2.js"></script>
            <link rel="stylesheet" href="css/normalize.css"/>
            <link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
            <script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
            <script src="js/dataTables.jqueryui.min.js" type="text/javascript"></script>
            <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">

            <link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
            <link rel="stylesheet" href="css/custom.css"/>
            <link rel="stylesheet" href="css/notificaciones.css" type="text/css" />
            <script type="text/javascript">
              $(document).ready(function () {
                  var i = 1;
                  $('#tabla thead th').each(function () {
                      if (i != 1) {
                          var title = $(this).text();
                          switch (i) {
                              case 3:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 4:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 5:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 6:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 6:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 7:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 8:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 9:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 10:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 11:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 12:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 13:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 14:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 15:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 16:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 17:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 18:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 19:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 20:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 21:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 22:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 23:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 24:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 25:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 26:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 27:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 28:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 29:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 30:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 31:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 32:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 33:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 34:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 35:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 36:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 37:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 38:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 39:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 40:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 41:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 42:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 43:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 44:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 45:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 46:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 47:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 48:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 49:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 50:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                          }
                          i = i + 1;
                      } else {
                          i = i + 1;
                      }
                  });

                  // DataTable
                  var table = $('#tabla').DataTable({
                      "autoFill": true,
                      "scrollX": true,
                      "pageLength": 5,
                      "language": {
                          "lengthMenu": "Mostrar _MENU_ registros",
                          "zeroRecords": "No Existen Registros...",
                          "info": "Página _PAGE_ de _PAGES_ ",
                          "infoEmpty": "No existen datos",
                          "infoFiltered": "(Filtrado de _MAX_ registros)",
                          "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ registros", "sInfoEmpty": "Mostrando 0 - 0 de 0 registros"
                      },
                      'columnDefs': [{
                              'targets': 0,
                              'searchable': false,
                              'orderable': false,
                              'className': 'dt-body-center'
                          }]
                  });
                  var i = 0;
                  table.columns().every(function () {
                      var that = this;
                      if (i != 0) {
                          $('input', this.header()).on('keyup change', function () {
                              if (that.search() !== this.value) {
                                  that
                                          .search(this.value)
                                          .draw();
                              }
                          });
                          i = i + 1;
                      } else {
                          i = i + 1;
                      }
                  });
              });
            </script>

            <style>
                /* Remove the navbar's default margin-bottom and rounded borders */
                .navbar {
                    margin-bottom: 0;
                    border-radius: 0;
                }

                /* Set height of the grid so .sidenav can be 100% (adjust as needed) */
                .row.content {height: 510px}

                /* Set gray background color and 100% height */
                .sidenav {
                    padding-top: 20px;
                    background-color: #f1f1f1;
                    height: 100%;
                }

                /* Set black background color, white text and some padding */
                footer {
                    background-color: #555;
                    color: white;
                    padding: 15px;
                }

                /* On small screens, set height to 'auto' for sidenav and grid */
                @media screen and (max-width: 767px) {
                    .sidenav {
                        height: auto;
                        padding: 15px;
                    }
                    .row.content {height:auto;}
                }
            </style>
        </head>
        <div class="col-md-14">
            <img src="RECURSOS/TOP/Fondo---Top.png">
           <div align="right" style="margin-top:-86px">
                    <style>
                    .containerdiv {  position: relative; } 
                    .cornerimage { position: absolute; top: 1px; right: 80px;  } 
                    .cornerVersion { position: absolute; top: 55px; right: 128px; font-size: 15px; color:white; font-family: -webkit-body;} 
                    </style>

                     <div class="containerdiv">
                         <img border="0" src="RECURSOS/TOP/Caja---Cliente.png" alt=""">
                         <img class="cornerimage" border="0" src="RECURSOS/TOP/Logos-Sigiep---Blanco.png" alt="">
                         <label class="cornerVersion"><i><strong>Versión <?=$version?></strong></i></label>
                     <div>
                </div>
            <div class="form-group form-inline " align="left" style="margin-top:-87px; height: 76px">
                <?php
                #**Buscar Rol
                $r = "SELECT * FROM gs_usuario u 
                LEFT JOIN gs_rol r ON u.rol = r.id_unico 
                lEFT jOIN gs_privilegios_rol pr ON pr.rol = r.id_unico
                lEFT jOIN gs_menu m ON pr.menu = m.id_unico
                WHERE m.nombre= 'DESEMBOLSOS' and u.id_unico =" . $_SESSION['id_usuario'];
                $r = $mysqli->query($r);
                $r = mysqli_num_rows($r);
                if ($r > 0) {
                    ?>
                    <img src="RECURSOS/TOP/Caja---Logo.png">
                    <a href="index2.php">
                        <img style="margin-left:-400px;max-width: 45px; margin-top: -40px" src="img/home.png" style="max-width: 10%">
                    </a>
                    <label class="form-group form-inline" style="font-size: 40px; color:white; font-family: -webkit-body; margin-left:21px; margin-top: -40px"><?php echo $ah; ?></label>
                    <label class="form-group form-inline" style="font-size: 15px; color:white; font-family: -webkit-body; margin-left:-200px; margin-top: 30px; width: 250px; text-align: center;line-height: 12px;"><i><strong><?php echo ucwords(mb_strtolower($ncom)) ?></strong></i></label>
                    <div align="left" class="form-group form-inline" style="margin-bottom: 10px; margin-left:450px">
                        <p class="form-group form-inline" style="color:white; font-size: 15px; font-family: cursive">
                        <label style="font-size: 40px; color:white; font-family: -webkit-body"><?php# echo $ah; ?></label>
                    </div>   
                    <div align="right" class="form-group form-inline" style="margin-bottom: 10px; margin-left:-400px">
                        <div id="quickNav" onclick="mostrar();">
                            <input type="hidden" id="identificador" value="0">
                            <ul>
                                <li class="quickNavNotification" style="width:50px">
                                    <a href="#menuPie" class="menu"><span class=""><img src="images/not.png" style="width: 55px; margin-left:70px; margin-top:1px" ></span></a>
                                    <?php
                                    #***Count Desembolsos Pendientes
                                    $des = "SELECT COUNT(*)
                                    FROM gf_desembolsos d 
                                    WHERE d.numero_cdp IS  NULL 
                                    AND d.numero_registro IS  NULL 
                                    AND d.obligacion IS  NULL ";
                                    $des = $mysqli->query($des);
                                    $des = mysqli_fetch_row($des);
                                    $num = $des[0];
                                    if ($num == 0) {
                                        
                                    } else {?>
                                        <span class="alert" ><?php echo $num ?></span>
                                    <?php } ?>
                                    <div id="menuPie" class="menu-container" style="display:none; margin-left:-100px;">
                                        <div class="menu-content cf"> 
                                            <div class="qnc">
                                                <p style="font-size:15px"><strong><i>Créditos Por Desembolsar</i></strong></p>
                                                <?php
                                                #*******Buscar Creditos Por Desembolso
                                                $dese = "SELECT d.id_unico, d.credito, DATE_FORMAT(d.fecha,'%d/%m/%Y'),  
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
                                                     t.apellidodos)) AS NOMBRE
                                                FROM gf_desembolsos d 
                                                LEFT JOIN gf_tercero t ON d.tercero = t.id_unico 
                                                WHERE d.numero_cdp IS  NULL 
                                                AND d.numero_registro IS  NULL 
                                                AND d.obligacion IS  NULL 
                                                ORDER BY d.credito ASC ";
                                                $dese = $mysqli->query($dese);
                                                if (mysqli_num_rows($dese) > 0) {
                                                    while ($rowD = mysqli_fetch_row($dese)) {
                                                        ?>
                                                        <a href="GF_DESEMBOLSOS.php?des=<?php echo $rowD[0] ?>" class="qnc_item">
                                                            <div class="qnc_content">
                                                                <span class="qnc_title">Crédito N° <?php echo $rowD[1] ?></span>
                                                                <span class="qnc_preview"><?php echo $rowD[2] ?></span>
                                                                <span class="qnc_time"><?php echo ucwords(mb_strtolower($rowD[3])) ?></span>
                                                            </div> <!-- .qnc_content -->
                                                        </a>
                                                <?php }} else { ?>
                                                    <a href="#" class="qnc_item">
                                                        <div class="qnc_content">
                                                            <span class="qnc_title">No hay Desembolsos Pendientes</span>
                                                        </div> <!-- .qnc_content -->
                                                    </a>
                                                <?php } ?>
                                                <a href="GF_DESEMBOLSOS_PEN.php" class="qnc_more">Ver Todos Desembolsos Pendientes</a>
                                            </div> <!-- .qnc -->
                                        </div>
                                    </div>        
                                </li>
                            </ul>   
                        </div> <!-- .quickNav -->
                        <script>
                            function mostrar() {
                                var i = $("#identificador").val();
                                var cl = document.getElementById("menuPie");

                                if (i == 1) {
                                    $("#identificador").val('0');
                                    cl.style.display = 'none';
                                } else {
                                    $("#identificador").val('1');
                                    cl.style.display = 'block';
                                }
                            }
                        </script>
                        <div id="footer">

                        </div>
                    </div>
                    <?php } else { ?>
                    <img src="RECURSOS/TOP/Caja---Logo.png">

                    <a href="index2.php">
                        <img style="margin-left:-400px;max-width: 45px; margin-top: -40px" src="img/home.png" style="max-width: 10%">
                    </a>
                    <label class="form-group form-inline" style="font-size: 40px; color:white; font-family: -webkit-body; margin-left:21px; margin-top: -35px"><?php echo $ah; ?></label>
                    <label class="form-group form-inline" style="font-size: 15px; color:white; font-family: -webkit-body; margin-left:-200px; margin-top: 30px; width: 250px; text-align: center;line-height: 12px;"><i><strong><?php echo ucwords(mb_strtolower($ncom)); ?></strong></i></label>
            
                    <div align="left" class="form-group form-inline" style="margin-bottom: 10px; margin-left:600px">
                        <p class="form-group form-inline" style="color:white; font-size: 15px; font-family: cursive">
                    </div>  
                    <?php } ?>
            </div>
        </div> 
        <script type="text/javascript" src="js/txtValida.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <script src="js/prefixfree.min.js"></script>
        <script src="js/modernizr.js"></script>
        <link href="skins/page.css" rel="stylesheet" />
        <link href="skins/blue/accordion-menu.css" rel="stylesheet" />
        <style>
            ul li{margin:10px 0;}
        </style>
<?php }  ?>