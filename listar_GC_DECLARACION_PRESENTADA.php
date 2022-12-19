<?php
    #01/06/2017 --- Nestor B --- se modifico  la consulta que trae la informacion.
    require_once './Conexion/conexion.php';
    require_once ('./Conexion/conexion.php');
    #session_start();
    require_once './head_listar.php';
    $anno = $_SESSION['anno'];
   
    $id = $_GET['id']; 

    $sql = "SELECT DISTINCT d.id_unico,
                    d.cod_dec,
                    d.fecha,
                    ac.vigencia,
                    vc.vigencia
            FROM gc_declaracion d
            INNER JOIN gc_detalle_declaracion dd ON dd.declaracion = d.id_unico
            LEFT JOIN gc_anno_comercial ac ON d.periodo = ac.id_unico
            LEFT JOIN gc_vigencia_comercial vc ON d.vigencia = vc.id_unico
            LEFT JOIN gc_recaudo_comercial rc ON rc.declaracion = d.id_unico
            WHERE md5(d.contribuyente) = '$id' ";

    $resultado = $mysqli->query($sql);
 
    $sql1="SELECT c.id_unico,c.codigo_mat,c.codigo_mat_ant,
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
                                c.tercero,
                                t.numeroidentificacion,
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
                                ter.apellidodos)) AS nombreRL,
                                ter.numeroidentificacion,
                                ter.id_unico,
                                c.estado,
                                ec.nombre
        FROM gc_contribuyente c
        LEFT JOIN gc_estado_contribuyente ec ON c.estado = ec.id_unico
        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
        LEFT JOIN gf_tercero ter ON ter.id_unico=c.repre_legal
        WHERE md5(c.id_unico) = '$id'";

$resultado1  = $mysqli->query($sql1);
$rowC = mysqli_fetch_row($resultado1);

?>
        <title>Declaraciones presentadas</title>
        <script src="dist/jquery.validate.js"></script>
        <!-- Librerias de carga para el datapicker -->
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <!-- select2 -->
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
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
    </head>
    <body >
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Declaraciones Presentadas</h2>
                    <a href="modificar_GC_CONTRIBUYENTE.php?id=<?php echo $id; ?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <input type="hidden" name="id" value="<?php echo $rowC[0] ?>">
                   

                    <h5 id="forma-titulo3a" align="center" style="width:95%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-10px;  background-color: #0e315a; color: white; border-radius: 5px;"><?php echo "Contribuyente: ".$rowC[7]." - ". ucwords(mb_strtolower($rowC[3])) ?></h5>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-5px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        <td class="cabeza"><strong>Código</strong></td>
                                        <td class="cabeza"><strong>Fecha</strong></td>
                                        <td class="cabeza"><strong>Periodo Gravable</strong></td>
                                        <td class="cabeza"><strong>Vigencia</strong></td>
                                        <td class="cabeza"><strong>Pago</strong></td>
                                        <td class="cabeza"><strong>Fecha Pago</strong></td>
                                        <td class="cabeza"><strong>Recaudo</strong></td>
                                        <td class="cabeza"><strong>Cuenta Bancaria</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th class="cabeza">Código</th>
                                        <th class="cabeza">Fecha</th>
                                        <th class="cabeza">Periodo Gravable</th>
                                        <th class="cabeza">Vigencia</th>
                                        <th class="cabeza">Pago</th>
                                        <th class="cabeza">Fecha Pago</th>
                                        <th class="cabeza">Recaudo</th>
                                        <th class="cabeza">Cuenta Bancaria</th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?php
                                $consec = 0;
                               
                                while ($row = mysqli_fetch_row($resultado)) {
                                    
                                    $cid      = $row[0];
                                    $ccod     = $row[1];
                                    $fdec     = date("d/m/Y", strtotime($row[2]));
                                    #$cnomb    = $row[2];
                                    $per     = $row[3];

                                    $vig     = $row[4];
                                    $consec++;
                                    $recid = "rec".$consec;


                                    $recaudo = "SELECT rc.valor, rc.fecha, rc.id_unico FROM gc_recaudo_comercial rc
                                    INNER JOIN gc_detalle_recaudo dr
                                    ON dr.recaudo = rc.id_unico
                                     WHERE rc.declaracion = '$cid'";
                                    $recau = $mysqli->query($recaudo);
                                    $nrec = mysqli_num_rows($recau);
                                     $idRec = "#".$recid; 

                                    if($nrec > 0){

                                        $rec = mysqli_fetch_row($recau);
                                        $xx = "SI";
                                        $frec     = date("d/m/Y", strtotime($rec[1]));
                                        $CuentaBancaria = "SELECT CONCAT(cb.numerocuenta,' - ',cb.descripcion,' - ',t.razonsocial) FROM gc_recaudo_comercial rc
                                                          LEFT JOIN gf_cuenta_bancaria cb ON rc.cuenta_ban = cb.id_unico
                                                          LEFT JOIN gf_tercero t ON cb.banco = t.id_unico
                                                          WHERE rc.declaracion = '$cid'";
                                        $CuentaBan = $mysqli->query($CuentaBancaria);
                                        $CB = mysqli_fetch_row($CuentaBan);
                                    
                                    }else{
                                        $rec[0] = 0;
                                        $rec[1] = "";
                                        $rec[2] = "";
                                        $frec  = "";
                                        $xx = "NO";
                                        $CB[0] = "";
                                    }
                                   
                                   

                            ?>
                            <tr>
                                <input type="hidden" id="rec" value="<?php echo $rec[2] ?>">
                                <td class="campos" style="display: none;"><?php echo $row[0]?></td>
                                <td class="campos" align="center">

                                <!--Modificado #09/01/2019 |LORENA M. | Ver el informe de declaración por contribuyente.-->
                                 <a type="button" class="btn-g campos" href="informesComercio/generar_INF_DECLARACION_CONTRIBUYENTE.php?id=<?php echo md5($cid)?>" target="_blank"><i title="Ver Declaración" class="glyphicon glyphicon-print" ></i>
                                 </a>
                                 <?php 
                                   if($xx =="SI"){
                                 ?>
                                 <a id="<?php echo $recid ?>" type="button" class="btn-g campos" href="GF_RECAUDOS_CONTRIBUYENTE.php?id=<?php echo md5($cid)?>"  >
                                        <i title="Ver Recaudo" class="glyphicon glyphicon-usd" ></i>
                                 </a>

                                 <?php 
                                   } else{                            
                                 ?>

                                <a id="eliminar" type="button" href="#" class="btn-e" onclick="javascript:eliminar(<?php echo $cid;?>);">
                                   <i title="Eliminar declaración" class="glyphicon glyphicon-trash" ></i>
                                 </a>
                                  <?php 
                                   }                         
                                 ?>
                                 </td>
                                <td class="campos" align="right"><?php echo $ccod?></td>
                                <td class="campos" align="center"><?php echo $fdec?></td>
                                <td class="campos" align="center"><?php echo $per?></td>
                                <td class="campos" align="center"><?php echo $vig?></td>
                                <td class="campos" align="center"><?php echo $xx?></td>
                                <td class="campos" align="center"><?php echo $frec?></td>
                                <td class="campos" align="right"><?php echo number_format($rec[0],2,'.',',')?></td>
                                <td class="campos" align="left"><?php echo $CB[0]?></td>
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
        <script>
            function abrirRec(id_dec){
                var id = id_dec;
                $("#LiqReca").modal('show');

                <?php echo $row[0] ?>
            }
        </script>
        <?php require_once './footer.php'; ?>
        <div class="modal fade" id="LiqReca" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content" style="width: 450px;">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Registrar Recaudo</h4>
                    </div>
                    <div class="modal-body" >
                        <div class="row text-left">
                            <form class="form-horizontal" id="formu" name="formu" method="post" action="jsonComercio/registrarRecaudoJson.php">
                                <div class="form-group" >
                                    <input type="hidden" name="txtTper" value="<?php echo $periodi ?>">
                                    <input type="hidden" name="txtTdec1" value="<?php echo $TipoDC ?>">
                                    <input type="hidden" name="txtfecD" value="<?php echo $FDEC ?>">
                                    <input type="hidden" name="txtidDec" value="<?php echo $cid ?>">
                                    <input type="hidden" name="X" value="1">
                                    <label for="banco" class="control-label col-sm-4 col-md-4 col-lg-4 text-right"><strong class="obligado">*</strong>Banco:</label>
                                        <?php
                                            $per = "SELECT cb.id_unico, CONCAT(cb.numerocuenta,' - ',t.razonsocial)
                                                    FROM gf_cuenta_bancaria cb
                                                    lEFT JOIN  gf_cuenta_bancaria_tercero cbt ON cbt.cuentabancaria = cb.id_unico
                                                    lEFT JOIN  gf_tercero t ON cb.banco = t.id_unico
                                                    WHERE cbt.tercero = 1";
                                            $periodo = $mysqli->query($per);
                                        ?>
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <select  id="banco" name="banco" class="form-control select2" title="Seleccione el Banco" required>
                                            <option value="" >Banco</option>
                                            <?php
                                                while($rowE = mysqli_fetch_row($periodo)){
                                                    echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <script type="text/javascript">
                                                $(document).ready(function() {
                                                    $("#datepicker").datepicker();
                                                });
                                            </script>
                                    <label for="sltFecha" class="col-sm-4 col-md-4 col-lg-4 control-label text-right" ><strong class="obligado">*</strong>Fecha:</label>
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <input name="sltFecha" id="sltFecha" title="Ingrese Fecha select2 " type="text"  class="form-control "   placeholder="Ingrese la fecha">
                                    </div>
                                </div>
                                <div class="form-group"  ">

                                    <label for="txtconsg" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" >Consignación o Pago:</label>
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <input name="txtconsg" id="txtconsg" title="Ingrese Número Consignación " type="text" class="form-control " placeholder="Ingrese Número Consignación o Pago ">
                                    </div>
                                </div>
                                <div class="form-group"  ">

                                    <label for="txtnum" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Declaración:</label>
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <input name="txtnum" id="txtnum"  type="text" class="form-control " value="<?php echo $ccod ?>"  readonly>
                                    </div>
                                </div>
                                <div class="form-group"  align="left">
                                    <label for="sltTipoR" class="control-label col-sm-4 col-md-4 col-lg-4 text-right" ><strong class="obligado">*</strong>Tipo Recaudo:</label>
                                        <?php
                                            $tiporec = "SELECT id_unico, nombre FROM gc_tipo_recaudo WHERE id_unico = 1 ";
                                            $treca = $mysqli->query($tiporec);
                                        ?>
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <select  id="sltTipoR" name="sltTipoR" class="form-control select2" title="Seleccione el Tipo de Recaudo" >
                                            <!--<option >Tipo Recaudo</option>-->
                                            <?php
                                                while($rowTR = mysqli_fetch_row($treca)){
                                                    echo "<option value=".$rowTR[0].">".$rowTR[1]."</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>




                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="guardar" class="btn"  style="color: #000; margin-top: 2px"  title="Guardar Recaudo"><li class="glyphicon glyphicon-floppy-disk" ></button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" title="Cancelar"><li class="glyphicon glyphicon-remove"></li></button>
                    </div>
                </div>
            </div> 
           

            <script>
                $('#guardar').click(function(e){
                    var cbanco  = $("#banco").val();
                    var fecha  = $("#sltFecha").val();
                    var pago   = $("#txtconsg").val();
                    var tipo   = $("#sltTipoR").val();
                    if(cbanco.length > 0 && fecha.length > 0 ){
                        $('#formu').submit();
                    }else{
                        if(cbanco == ""){
                            //$("#banco").parents(".col-sm-6").addClass('has-error');
                            $("#s2id_banco").addClass('has-error');
                            //$("label[for='banco']").addClass('has-error');
                        }

                        if(fecha == ""){
                            $("#sltFecha").parents(".col-sm-6").addClass('has-error');
                        }

                        if(pago == ""){
                            $("#txtconsg").parents(".col-sm-6").addClass('has-error');
                        }

                        if(tipo == ""){
                            $("#s2id_sltTipoR").parents(".col-sm-6").addClass('has-error');
                        }
                    }
                });


            </script>

        </div>
                 
    <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar la Declaración seleccionada?</p>
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

        <!--Script que dan estilo al formulario-->

        <script type="text/javascript" src="js/menu.js"></script>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <!--Script que envia los datos para la eliminación-->
    <script type="text/javascript">
          function eliminar(id)
            {var result = '';             
                $("#myModal").modal('show');             
                $("#ver").click(function(){
                    $("#mymodal").modal('hide');
                    $.ajax({
                        type:"GET",
                        url:"jsonComercio/eliminarDeclaracionJson.php?id="+id,
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

        <script type="text/javascript">
            function modal()
            {
                $("#myModal").modal('show');
            }
        </script>
        <!--Actualiza la página-->
        <script type="text/javascript">
            $('#ver1').click(function(){
                document.location = 'listar_GC_DECLARACION_PRESENTADA.php?id=<?php echo md5($rowC[0]); ?>';
            });
        </script>

        <script src="js/bootstrap.min.js"></script>

        <script type="text/javascript" src="js/select2.js"></script>
        <script type="text/javascript">
            $("#banco").select2();
        </script>
    </body>
</html>
