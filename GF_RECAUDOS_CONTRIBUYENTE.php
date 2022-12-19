<?php
#######################################################################################################
# ************************************   CREADO POR  ******************************************* #
#######################################################################################################
#21/01/2019 |LORENA MORENO. | Buscar Por Contribuyente
#######################################################################################################
require_once 'Conexion/conexion.php';
require_once 'Conexion/ConexionPDO.php';
require_once './jsonPptal/funcionesPptal.php';
require_once 'head_listar.php';
$con = new ConexionPDO();
$anno  = $_SESSION['anno'];

$id = $_GET['id'];

$sqlRecaudo = "SELECT DISTINCT dr.recaudo, 
                         rc.consecutivo,
                         DATE_FORMAT(rc.fecha,'%d/%m/%Y'), 
                         d.cod_dec,
                         rc.valor
                FROM gc_recaudo_comercial rc
                INNER JOIN gc_detalle_recaudo dr
                ON dr.recaudo = rc.id_unico
                INNER JOIN gc_declaracion d
                ON rc.declaracion = d.id_unico 
                AND rc.clase = 1 AND md5(d.id_unico) = '$id'";

 $resultRec = $mysqli->query($sqlRecaudo);  


$sql1 ="SELECT t.numeroidentificacion, 
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
                t.apellidodos)) AS NOMBRETERCERO,
                d.cod_dec,  
                d.contribuyente,
                d.id_unico
        FROM gc_declaracion d LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico
        LEFT JOIN gc_estado_contribuyente ec ON c.estado = ec.id_unico
        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
        LEFT JOIN gf_tercero ter ON ter.id_unico=c.repre_legal
        WHERE md5(d.id_unico) = '$id'";  

$resultado1  = $mysqli->query($sql1);
$rowC = mysqli_fetch_row($resultado1);                            

?>
<title>Listar Recaudos Comercio</title>	
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script> 
<link href="css/select/select2.min.css" rel="stylesheet">
 <style>
        .btn-g{
           padding: 1px 6px !important; 
           color: #000000d6 !important;
         }
        .btn-g :hover
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
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-bottom:20px; margin-right:4px; margin-left:4px;"><?php echo "Recaudos - Declaración No: ".$rowC[2] ?></h2>
                <a href="listar_GC_DECLARACION_PRESENTADA.php?id=<?php echo md5($rowC[3]); ?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                 <h5 id="forma-titulo3a" align="center" style="width:95%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-10px;  background-color: #0e315a; color: white; border-radius: 5px;"><?php echo "Contribuyente: ".$rowC[0]." - ". ucwords(mb_strtolower($rowC[1]))?></h5>   
                 <input type="hidden" name="id" value="<?php echo $rowC[3] ?>">           
               
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-5px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px" align="center"></td>
                                    <td><strong>Recaudo No.</strong></td>
                                    <td><strong>Fecha Pago</strong></td>
                                    <td><strong>Valor</strong></td>
                                    <td><strong>Comprobante De Ingreso</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="30px" align="center"></th>
                                    <th>Recaudo No.</th>
                                    <th>Fecha Pago</th> 
                                    <th>Valor</th>        
                                    <th>Comprobante De Ingreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php                                 
                                 while ($row = mysqli_fetch_row($resultRec)){

                                      $cid =  $row[0];             
                                      $numRec = $row[1];
                                      $fechaRec = $row[2];
                                      $vlrRec = $row[4];
                                       /* verfica si tiene comprobante contable*/
                                       $sqlCpte="SELECT DISTINCT cn.id_unico, cn.numero, dpp.comprobantepptal 
                                         FROM gf_comprobante_cnt cn 
                                         LEFT JOIN gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante 
                                         LEFT JOIN gc_detalle_recaudo dp ON dp.detalle_cnt = dc.id_unico 
                                         LEFT JOIN gf_detalle_comprobante_pptal dpp ON dc.detallecomprobantepptal = dpp.id_unico 
                                         WHERE dp.recaudo = '$cid'";
                                         $resultado2 = $mysqli->query($sqlCpte);
                                         $nctpe = mysqli_num_rows($resultado2);

                                       if($nctpe > 0){
                                         $xx = "SI";

                                        $rowP = mysqli_fetch_row($resultado2);
                                            $numCpte = $rowP[1];                                        
                                        }else{
                                            $numCpte = "";
                                            $xx = "NO";                                       
                                        }
                                    
                                ?>                               
                             
                           <tr>
                            <input type="hidden" id="cpte" value="<?php echo $numCpte ?>">

                            <td class="campos" style="display: none;"><?php echo $row[0]?></td>
                            <td class="campos" align="center">   <?php 
                            if($xx =="SI"){
                                 ?>                          
                             <a id="vercpte" type="button" class="btn-g campos" onclick="javascript:ver(<?php echo $rowP[0];?>,<?php echo $rowP[2];?>);"
                              target="_blank">
                                    <i title="Ver Comprobante contable" class="glyphicon glyphicon-eye-open" ></i>
                                </a>
                                 <?php 
                                   }                             
                                 ?>
                                 <?php 
                                   if($xx =="NO"){
                                 ?>
                                 <a id="eliminar" type="button" href="#" class="btn-e" onclick="javascript:eliminar(<?php echo $cid;?>);">
                                   <i title="Eliminar recaudo" class="glyphicon glyphicon-trash"></i>
                                 </a>
                                  <?php 
                                   }                             
                                 ?>
                             </td>
                             <td class="campos" align="center"><?php echo $numRec ?></td>
                             <td class="campos" align="center"><?php echo $fechaRec ?></td>
                             <td class="campos" align="center"><?php echo number_format($vlrRec,2,'.',',') ?></td>
                             <td class="campos" align="center"><?php echo $numCpte  ?></td>                                       
                             </tr>
                              
                              <?php
                                }
                               ?>
                            </tbody>	
                        </table>
                    </div>                   
                </div>               
            </div>
        </div>
    </div>
   <div class="modal fade" id="mdlMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnAceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnCancelar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
     <!-- Modal cuando la declaración no tiene recaudo -->
            <div class="modal fade" id="norec" role="dialog" align="center" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div id="forma-modal" class="modal-header">
                            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                        </div>
                        <div class="modal-body" style="margin-top: 8px">
                            <p>La declaración aún no presenta recaudos.</p>
                        </div>
                        <div id="forma-modal" class="modal-footer">
                            <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>
    <?php require_once 'footer.php'; ?>
    <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el Recaudo seleccionado?</p>
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
 
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="js/select/select2.full.js"></script>
    <script>
              
        $(document).ready(function () {
            $(".select2_single").select2({
                allowClear: true,
            });
        });
    </script>
    <!--Script que envia los datos para la eliminación-->
    <script type="text/javascript">
          function eliminar(id)
            {var result = '';             
                $("#myModal").modal('show');             
                $("#ver").click(function(){
                    $("#mymodal").modal('hide');
                    $.ajax({
                        type:"GET",
                        url:"jsonComercio/eliminarRecaudoContribuyenteJson.php?id="+id,
                        success: function (data) {
                            result = JSON.parse(data);
                            if(result==true)                               
                               $("#myModal1").modal('show');
                            else
                               $("#myModal2").modal('show');
                        },
                        error:function (data)
                        {
                          alert(data);
                        }   
                    });
                });
            }
      </script>

    <script>
        function ver(id, idp){
            var form_data = { action: 9, id:id, idp:idp };
            $.ajax({
                type: "POST",
                url: "jsonPptal/gf_interfaz_ComercioJson.php",
                data: form_data,
                success: function(response)
                {
                    window.open("registrar_GF_COMPROBANTE_INGRESO.php");
                }
            })
        }
    </script>
    <script>
        function jsRemoveWindowLoad() {
            // eliminamos el div que bloquea pantalla
            $("#WindowLoad").remove(); 
        }

        function jsShowWindowLoad(mensaje) {
        //eliminamos si existe un div ya bloqueando
        jsRemoveWindowLoad(); 
        //si no enviamos mensaje se pondra este por defecto
        if (mensaje === undefined) mensaje = "Procesando la información<br>Espere por favor"; 
        //centrar imagen gif
        height = 20;//El div del titulo, para que se vea mas arriba (H)
        var ancho = 0;
        var alto = 0; 
        //obtenemos el ancho y alto de la ventana de nuestro navegador, compatible con todos los navegadores
        if (window.innerWidth == undefined) ancho = window.screen.width;
        else ancho = window.innerWidth;
        if (window.innerHeight == undefined) alto = window.screen.height;
        else alto = window.innerHeight; 
        //operación necesaria para centrar el div que muestra el mensaje
        var heightdivsito = alto/2 - parseInt(height)/2;//Se utiliza en el margen superior, para centrar 
       //imagen que aparece mientras nuestro div es mostrado y da apariencia de cargando
        imgCentro = "<div style='text-align:center;height:" + alto + "px;'><div  style='color:#FFFFFF;margin-top:" + heightdivsito + "px; font-size:20px;font-weight:bold;color:#1075C1'>" + mensaje + "</div><img src='img/loading.gif'/></div>"; 
            //creamos el div que bloquea grande------------------------------------------
            div = document.createElement("div");
            div.id = "WindowLoad";
            div.style.width = ancho + "px";
            div.style.height = alto + "px";        
            $("body").append(div); 
            //creamos un input text para que el foco se plasme en este y el usuario no pueda escribir en nada de atras
            input = document.createElement("input");
            input.id = "focusInput";
            input.type = "text"; 
            //asignamos el div que bloquea
            $("#WindowLoad").append(input); 
            //asignamos el foco y ocultamos el input text
            $("#focusInput").focus();
            $("#focusInput").hide(); 
            //centramos el div del texto
            $("#WindowLoad").html(imgCentro);

    }
    </script>
   
    <!--Actualiza la página-->
  <script type="text/javascript">
    
      $('#ver1').click(function(){
        document.location = 'GF_RECAUDOS_CONTRIBUYENTE.php?id=<?php echo md5($rowC[4]); ?>';
      });
    
  </script>

    <style>
        #WindowLoad{
            position:fixed;
            top:0px;
            left:0px;
            z-index:3200;
            filter:alpha(opacity=80);
           -moz-opacity:80;
            opacity:0.80;
            background:#FFF;
        }
    </style>
</body>
</html>
