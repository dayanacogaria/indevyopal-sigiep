 <?php
require_once ('./head_listar.php');
require_once ('./Conexion/conexion.php');
require_once('./jsonSistema/funcionCierre.php');
require_once('./jsonPptal/funcionesPptal.php');
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$num_anno   = anno($_SESSION['anno']);
?>     
    <title>Comprobante Contable</title>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <!-- select2 -->
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css">
    <script type="text/javascript">
    $(function(){
        var fecha = new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth() + 1;
        if(dia < 10){
            dia = "0" + dia;
        }
        if(mes < 10){
            mes = "0" + mes;
        }
        //var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            yearRange: '<?php echo $num_anno.':'.$num_anno;?>', 
            maxDate: '31/12/<?php echo $num_anno?>',
            minDate: '01/01/<?php echo $num_anno?>'
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha").datepicker({changeMonth: true}).val();        
        $("#fechaM").datepicker({changeMonth: true}).val(); 
    });
    </script>    
    <style>
/*Estilos tabla*/
table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
table.dataTable tbody td,table.dataTable tbody td{padding:1px}
.dataTables_wrapper .ui-toolbar{padding:2px}
/*Campos dinamicos*/
.campoD:active {
    border-color: #66afe9;
    outline: 0;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);            
}
.campoD:hover{
    cursor: pointer;
}
/*Campos dinamicos label*/
.valorLabel{
    font-size: 10px;
}
.valorLabel:hover{
    cursor: pointer;
    color:#1155CC;
}

/*td de la tabla*/
.campos{
    padding: 0px;
    font-size: 10px
}
/*cuerpo*/
body{
    font-size: 10px
}
.form-control{
    padding: 2px;
}
.select2-container .select2-choice{
  height: 26px;
  padding: 0px;
}
</style> 
<style>
.cabeza{
    white-space:nowrap;
    padding: 20px;
}
.campos{
    padding:-20px;
}


</style>  
<script type="text/javascript">

  $(document).ready(function()
  {
    //Función que ejecuta consulta para verificar si el comprobante
    var id= $("#id").val();
    console.log(id);
    if(id==""){
        response =0;
    } else {
    var form_data = { case:21, id: id};
    $.ajax({
      type: "POST",
      url: "consultasBasicas/busquedas.php",
      data: form_data,
      success: function(response)
      {
          console.log(response);
          if(response==1){
              $("#btnNuevo").attr('disabled','disabled');
              $("#sltBuscar").attr('disabled','disabled');
              
          }
        document.getElementById("balanceo").value = response;
      }
    });
    }

  });

 </script>

<script type="text/javascript">
  //Evento mouseover sobre el menú para avisar al usuario en caso de que las fuentes estén desbalanceadas.
  $(document).ready(function()
  {
    $("#accordion").mouseover(function()
    {
      var balanceo = document.getElementById("balanceo").value;
      if(balanceo == 1)
      {
      $("#btnNuevo").attr('disabled','disabled');
      $("#sltBuscar").attr('disabled','disabled');
      $("#modDesBal").modal('show');
      $("#btnDesBal").focus();
    }
    });
  });
</script>

<script type="text/javascript">
  //Esta función muestra un mensaje modal al usuario al intentar dejar al página. Al detectar la poscición del cursor acercarse a cero, el borde superior de la página, muestra el mensaje diciendo que las fuentes están desbalanceadas en caso en que lo estén.
  function coordenadas(event) 
  {
    var y = event.clientY;
    var balanceo = $("#balanceo").val();
    if(balanceo == 1)
    {
      if(y >= 0 && y <= 20 )
      {
        $("#modDesBal").modal('show');
        $("#btnDesBal").focus();
      }
    }
  }
</script>
    </head>
    <body onMouseMove="coordenadas(event);">        
        <div class="container-fluid text-left">
            <div class="row content">
                <?php require_once('menu.php'); ?>
                <div class="col-sm-8 text-center" style="margin-top:-22px;">
                    <h2 class="tituloform" align="center" >Comprobante Contable</h2>
                    <div class="client-form contenedorForma " style="margin-top:-7px;">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarComprobanteContable.php" style="margin-bottom:-20px">
                            <input type="hidden" id="balanceo" value="0">
                            <input type="hidden" name="formulario" id="formulario" value="registrar_GF_COMPROBANTE_CONTABLE.php">
                            <?php   
                            #***Trae Id***#
                            if(!empty($_GET['id'])){
                                $bi = "SELECT id_unico FROM gf_comprobante_cnt WHERE md5(id_unico)='".$_GET['id']."'";
                                $bi = $mysqli->query($bi);
                                $bi = mysqli_fetch_row($bi);
                                $id = $bi[0];
                                $_SESSION['idNumeroC'] =$id;
                            }
                            $tercero = 0;
                            $nume = 0;
                            $fecha = '';
                            $idC = 0;
                            $tipoComprobante = '';
                            $idComprobante = 0;
                            $descripcion = "";
                            $numero  = "";
                            if (!empty($_SESSION['idNumeroC'])) {
                                #$nume = $_SESSION['num'];
                                $idComprobante = $_SESSION['idNumeroC'];
                                $sql="  
                                SELECT 
                                        cn.id_unico,
                                        cn.fecha,
                                        cn.tipocomprobante,
                                        cn.numero,
                                        cn.tercero,
                                        tr.id_unico,
                                        tr.nombreuno,
                                        tr.nombredos,
                                        tr.apellidouno,
                                        tr.apellidodos,
                                        tr.razonsocial,
                                        ti.nombre,
                                        tr.numeroidentificacion,
                                        ct.id_unico,
                                        ct.nombre,
                                        cc.id_unico,
                                        cc.nombre,
                                        cn.numerocontrato,
                                        ec.nombre,
                                        cn.descripcion,
                                        cn.id_unico 
                                FROM gf_comprobante_cnt cn
                                LEFT JOIN gf_tipo_comprobante ct ON cn.tipocomprobante = ct.id_unico
                                LEFT JOIN gf_tercero tr ON cn.tercero = tr.id_unico
                                LEFT JOIN gf_tipo_identificacion ti ON tr.tipoidentificacion = ti.id_unico
                                LEFT JOIN gf_clase_contrato cc ON cn.clasecontrato = cc.id_unico
                                LEFT JOIN gf_estado_comprobante_cnt ec ON cn.estado = ec.id_unico
                                WHERE cn.id_unico = '$idComprobante'";
                                $rs = $mysqli->query($sql);
                                $cn = mysqli_fetch_row($rs); 
                                $fecha = $cn[1];
                                $tipoComprobante = $cn[2];
                                $tercero = $cn[5];
                                $idC = $cn[0]; 
                                $descripcion = $cn[19];
                                $numero = $cn[3];
                                $numerocontrato = $cn[17];

                            }                    
                            ?>
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                                Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                            </p>
                            <?php if (!empty($_SESSION['idNumeroC'])) { ?>
                            <input type="hidden" name="id" id="id" value = "<?php echo $_SESSION['idNumeroC']?>">
                            <?php }  else { ?>
                            <input type="hidden" name="id" id="id" value="0">
                                <?php } ?>
                            <div class="form-group form-inline col-sm-12" style="margin-top: 5px; margin-left: 5px;">
                                <!-- Fecha -->
                                <label for="fecha" class="col-sm-2 control-label" style="margin-left:-20px">
                                    <strong class="obligado">*</strong>Fecha:
                                </label>                                
                                <input class="col-sm-2 input-sm" value="<?php if(!empty($fecha)){$fechaS = explode("-",$fecha); echo $fechaS[2].'/'.$fechaS[1].'/'.$fechaS[0];}else{} ?>" type="text" name="fecha" id="fecha" class="form-control" style="width:100px;height:26px" title="Ingrese la fecha" placeholder="Fecha" required readonly="true">                                                                                                
                                <!-- Tipo Comprobante -->
                                <label class="col-sm-2 control-label" for="sltTipoC">
                                    <strong class="obligado">*</strong>Tipo Comprobante:
                                </label>                                
                                <select class="col-sm-2 input-sm"  name="sltTipoC" id="sltTipoC" class="form-control" style="width:100px;height:26px;cursor: pointer;" title="Seleccione tipo de comprobante" required>
                                    <?php                                     
                                    if(!empty($tipoComprobante)){                                       
                                        $sqlTC = "SELECT DISTINCT id_unico,sigla,nombre FROM gf_tipo_comprobante WHERE id_unico = $tipoComprobante";
                                        $m = $mysqli->query($sqlTC);
                                        while($resc = mysqli_fetch_row($m)){
                                            echo '<option value="'.$resc[0].'">'.mb_strtoupper($resc[1]).' - '.ucwords(mb_strtolower($resc[2])).'</option>';
                                        }
                                        $sqlX = "SELECT DISTINCT id_unico,sigla, nombre FROM gf_tipo_comprobante WHERE id_unico != $cn[13] AND clasecontable=15 AND niif !=1 AND compania = $compania ";
                                        $resulta = $mysqli->query($sqlX); 
                                        while($d = mysqli_fetch_row($resulta)){
                                            echo '<option value="'.$d[0].'">'.mb_strtoupper($d[1]).' - '.ucwords(mb_strtolower($d[2])).'</option>';
                                        }                                       
                                    }else{?>
                                        <option value="">Tipo Combrobante</option>
                                        <?php 
                                        $sqlTC = "SELECT id_unico,sigla,nombre FROM gf_tipo_comprobante WHERE clasecontable=15 AND niif !=1 AND compania = $compania";
                                        $m = $mysqli->query($sqlTC);
                                        while($resc = mysqli_fetch_row($m)){
                                            echo '<option value="'.$resc[0].'">'.mb_strtoupper($resc[1]).' - '.ucwords(mb_strtolower($resc[2])).'</option>';
                                        }
                                    }
                                    ?>                                    
                                </select>  
                                <!-- Número de comprobante -->                                 
                                <label class="col-sm-2 control-label" for="txtNumero">
                                    <strong class="obligado">*</strong>N° Comprobante:
                                </label>
                                <!-- Número comprobante -->
                                <input class="col-sm-2 input-sm" type="text" name="txtNumero" id="txtNumero" class="form-control" maxlength="50" style="width:100px;height:26px" placeholder="N° comprobante" title="Número de comprobante" value="<?php if(!empty($numero)){ echo $numero;} ?>" required readonly="true">
                                </div><br/>
                                <div class="form-group form-inline col-sm-12" style="margin-top:-10px" >                                  
                                <label for="fecha" class="col-sm-2 control-label" >
                                    <strong class="obligado">*</strong>Tercero:
                                </label>
                                <select class="form-control col-sm-1 select2_single text-left" name="sltTercero" id="sltTercero" style="width:345px;height:26px" title="Seleccione tercero" required>
                                    <?php 
                                    if(!empty($cn[5])){ 
                                        $sql18 = "SELECT  IF(CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos) IS NULL 
                                                            OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                                            (ter.razonsocial),
                                                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos  )) AS NOMBRE, 
                                            ter.id_unico, CONCAT(ti.nombre,'  ',ter.numeroidentificacion) AS 'TipoD' , ter.digitoverficacion 
                                            FROM gf_tercero ter
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                            WHERE ter.id_unico=$cn[5]";
                                        $rs18 = $mysqli->query($sql18);
                                        $row18 = mysqli_fetch_row($rs18);
                                        if(empty($row18[3])) { 
                                        echo '<option value="'.$row18[1].'">'.ucwords(mb_strtolower($row18[0].' '.$row18[2])).'</option>';   
                                        } else {
                                            echo '<option value="'.$row18[1].'">'.ucwords(mb_strtolower($row18[0].' '.$row18[2].'-'.$row18[3])).'</option>';   
                                        }
                                        $sql19 = "SELECT  IF(CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos) IS NULL 
                                                            OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                                            (ter.razonsocial),
                                                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos  )) AS NOMBRE, 
                                            ter.id_unico, CONCAT(ti.nombre,'  ',ter.numeroidentificacion) AS 'TipoD', ter.digitoverficacion FROM gf_tercero ter
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion  
                                            WHERE ter.id_unico!=$cn[5] and ter.compania = $compania LIMIT 20";
                                        $rs19 = $mysqli->query($sql19);
                                        while($row19 = mysqli_fetch_row($rs19)){
                                            if(empty($row19[3])) { 
                                            echo '<option value="'.$row19[1].'">'.ucwords(mb_strtolower($row19[0].' '.$row19[2])).'</option>';
                                            } else {
                                                echo '<option value="'.$row19[1].'">'.ucwords(mb_strtolower($row19[0].' '.$row19[2].'-'.$row19[3])).'</option>';
                                            }
                                            
                                        }
                                    }else{
                                        echo '<option value="">Tercero</option>';
                                        $sql19 = "SELECT IF(CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos) IS NULL 
                                                            OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                                            (ter.razonsocial),
                                                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos  )) AS NOMBRE,
                                            ter.id_unico, CONCAT(ti.nombre,'  ',ter.numeroidentificacion) AS 'TipoD', ter.digitoverficacion  FROM gf_tercero ter
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion WHERE ter.compania = $compania LIMIT 20";
                                        $rs19 = $mysqli->query($sql19);
                                        while($row19 = mysqli_fetch_row($rs19)){
                                            if(empty($row19[3])) { 
                                            echo '<option value="'.$row19[1].'">'.ucwords(mb_strtolower($row19[0].' '.$row19[2])).'</option>';
                                            } else {
                                                echo '<option value="'.$row19[1].'">'.ucwords(mb_strtolower($row19[0].' '.$row19[2].'-'.$row19[3])).'</option>';
                                            }
                                        }   
                                    }
                                    ?>                                    
                                </select>  
                                <label class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Centro Costo:
                                </label>
                                <select class="col-sm-2 input-sm" name="sltCentroC" id="sltCentroC" class="form-control" style="width:100px;height:30%;cursor: pointer;" title="Seleccion centro costo" required>
                                    <?php 
                                    $c = $c = 0;
                                    if(!empty($_SESSION['centrocosto'])){                                        
                                        $centro = $_SESSION['centrocosto'];
                                        $sql78 = "SELECT CC.id_unico,CC.nombre FROM gf_centro_costo CC  WHERE CC.id_unico=$centro AND parametrizacionanno = $anno";
                                        $result78 = $mysqli->query($sql78);
                                        $cc = mysqli_fetch_row($result78);
                                        echo '<option value="'.$cc[0].'">'.ucwords(mb_strtolower($cc[1])).'</option>';
                                        $sql80 = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico != $cc[0] AND parametrizacionanno = $anno ORDER BY nombre ASC";
                                        $result80 = $mysqli->query($sql80);
                                        while($row80 = mysqli_fetch_row($result80)){ ?>
                                            <option value="<?php echo $row80[0]; ?>"><?php echo ucwords(mb_strtolower($row80[1])); ?></option>
                                        <?php }
                                        
                                    }else{ ?>
                                        <?php 
                                        $sqlC = "SELECT id_unico,nombre FROM gf_centro_costo WHERE nombre = 'varios' AND parametrizacionanno = $anno";
                                        $res = $mysqli->query($sqlC);
                                        $ress = mysqli_fetch_row($res);                                    
                                        ?>
                                        <option value="<?php echo $ress[0] ?>"><?php echo ucwords(mb_strtolower($ress[1])) ?></option>
                                        <?php                                     
                                        $sql = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE nombre != 'varios' AND parametrizacionanno = $anno ORDER BY nombre ASC";
                                        $result = $mysqli->query($sql);
                                        while($row = mysqli_fetch_row($result)){ ?>
                                            <option value="<?php echo $row[0]; ?>"><?php echo ucwords(mb_strtolower($row[1])); ?></option>
                                        <?php }
                                        ?>
                                        <?php }
                                        ?>                                    
                                </select>
                            </div>
                            <div class="form-group form-inline col-sm-12" style="margin-top:-10px" >                                  
                                <label class="col-sm-2 control-label" for="txtDescripcion">
                                    Descripción:
                                </label>
                                <?php if(!empty($_SESSION['idNumeroC']) && !empty($cn[19])){ ?>
                                 
                                <textarea class="col-sm-1" style="margin-top:-1px;height:26px;width:345px;" class="area" 
                                name="txtDescripcion" id="txtDescripcion"  maxlength="500" 
                                onkeypress="return txtValida(event,'num_car')" ><?php echo  $descripcion;?>
                                </textarea>   
                                <?php  } else{ ?> 
                                    <textarea class="col-sm-1" style="margin-top:-1px;height:26px;width:345px;" class="area" 
                                              name="txtDescripcion" id="txtDescripcion"  maxlength="500"  placeholder="Descripción"
                                onkeypress="return txtValida(event,'num_car')" >
                                </textarea>   
                                    <?php } ?>
                                <label class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Proyecto:
                                </label>
                                <select class="col-sm-2 input-sm" name="sltProyecto" id="sltProyecto" class="form-control" style="width:100px;height:26px;margin-left:1px;cursor: pointer;" title="Seleccione proyecto" required>
                                    <?php 
                                    if(!empty($_SESSION['proyecto'])){ ?>                                        
                                        <?php 
                                        $sql = "SELECT CC.id_unico,CC.nombre FROM gf_proyecto CC WHERE CC.id_unico=".$_SESSION['proyecto'];
                                        $result = $mysqli->query($sql);
                                        $pr = mysqli_fetch_row($result);
                                        echo '<option value="'.$pr[0].'">'.ucwords(mb_strtolower($pr[1])).'</option>';
                                        $sql = "SELECT id_unico,nombre FROM gf_proyecto WHERE id_unico != $pr[0] ORDER BY nombre ASC";
                                        $result = $mysqli->query($sql);                                        
                                        while($row=mysqli_fetch_row($result)){ ?>
                                            <option value="<?php echo $row[0]; ?>"><?php echo ucwords(mb_strtolower($row[1])); ?></option>
                                        <?php
                                        }
                                        ?>
                                    <?php 
                                    
                                        }else{ ?>
                                        <?php 
                                        $sqlP = "SELECT id_unico,nombre FROM gf_proyecto WHERE nombre = 'varios' ORDER BY nombre ASC";
                                        $repP = $mysqli->query($sqlP);
                                        $ressP = mysqli_fetch_row($repP);
                                        ?>
                                        <option value="<?php echo $ressP[0]; ?>">
                                            <?php echo ucwords(mb_strtolower($ressP[1])); ?>
                                        </option>
                                        <?php 
                                        $sql = "SELECT id_unico,nombre FROM gf_proyecto WHERE id_unico != $ressP[0] ORDER BY nombre ASC";
                                        $result = $mysqli->query($sql);
                                        while($row=mysqli_fetch_row($result)){ ?>
                                            <option value="<?php echo $row[0]; ?>"><?php echo ucwords(mb_strtolower($row[1])); ?></option>
                                        <?php
                                        }
                                        ?>
                                    <?php    
                                    }
                                    ?>                                                                                                            
                                </select>
                            </div><br/>
                            <div class="form-group form-inline col-sm-12" style="margin-top:-10px" >                                  
                                <label class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Tipo Contrato:
                                </label>
                                <select class="col-sm-1 input-sm" name="sltClaseCT" id="sltClaseCT" class="form-control" style="width:100px;height:26px;cursor: pointer;" title="Seleccione tipo contrato" required>
                                    <?php 
                                    if(!empty($cn[15])){
                                        if(!empty($cn[15])){
                                            echo '<option value="'.$cn[15].'">'.ucwords(mb_strtolower($cn[16])).'</option>';
                                            $sql = "SELECT id_unico,nombre FROM gf_clase_contrato WHERE id_unico != $cn[15] ORDER BY nombre ASC";
                                            $result = $mysqli->query($sql);
                                            while($row =  mysqli_fetch_row($result)){ 
                                                echo '<option value="'.$row[0].'">'.ucwords(mb_strtolower($row[1])).'</option>';
                                            }
                                        }else{
                                            echo '<option value="">Tipo Contrato</option>';
                                            $sqlCCC = "SELECT id_unico,nombre FROM gf_clase_contrato";
                                            $r = $mysqli->query($sqlCCC);
                                            while($x = mysqli_fetch_row($r)){
                                                echo '<option value="'.$x[0].'">'.$x[1].'</option>';
                                            }
                                        }                                                                                
                                    }else{?>
                                        <option value="">Tipo Contrato</option>
                                        <?php 
                                        $sqlCCC = "SELECT id_unico,nombre FROM gf_clase_contrato";
                                        $r = $mysqli->query($sqlCCC);
                                        while($x = mysqli_fetch_row($r)){
                                            echo '<option value="'.$x[0].'">'.$x[1].'</option>';
                                        }
                                        ?>
                                    <?php                                    
                                    }
                                    ?>                                    
                                </select>
                                <label class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>N° Contrato:
                                </label>
                                <?php if(!empty($numerocontrato)) { ?>
                                    <input class="col-sm-2 input-sm" type="text" name="txtNumeroCT" id="txtNumeroCT" class="form-control" style="width: 100px;height:26px;" title="Ingrese número de contrato" required placeholder="N° Contrato" value="<?php echo $numerocontrato ?>"/>
                                <?php }  else { ?>
                                    <input class="col-sm-2 input-sm" type="text" name="txtNumeroCT" id="txtNumeroCT" class="form-control" style="width: 100px;height:26px;" title="Ingrese número de contrato" required placeholder="N° Contrato" value=""/>
                                <?php } ?>
                                
                                <label class="col-sm-2 control-label">
                                    <strong class="obligado">*</strong>Estado:
                                </label>
                                <?php 
                                $sql = "SELECT id_unico,nombre FROM gf_estado_comprobante_cnt WHERE id_unico = 1";
                                $result = $mysqli->query($sql);
                                $row = mysqli_fetch_row($result);
                                ?>
                                <input class="col-sm-2" type="text" name="txtEstado" id="txtEstado" class="form-control" style="width:100px;height:26px;" value="<?php  if(!empty($_SESSION['num'])){echo $cn[18];}else{echo ucwords(mb_strtolower($row[1]));} ?>" title="Estado" placeholder="Estado" readonly/>
                            </div>
                            <div class="form-group form-inline col-sm-6" style="margin-top:-10px" >                                  
                                    <label for="sltBuscar" class="col-sm-4 control-label">
                                        Buscar Comprobante:
                                    </label>
                                <div class="form-group form-inline col-sm-4" >                                  
                                    <select name="sltTipoBuscar" id="sltTipoBuscar" title="Tipo Comprobante" class="select2_single form-control" style="width: 130px;">
                                        <option value="">Tipo Comprobante</option>
                                        <?php $sqlTC = "SELECT id_unico,sigla,nombre FROM gf_tipo_comprobante WHERE clasecontable=15 AND niif !=1 and compania = $compania";
                                        $m = $mysqli->query($sqlTC);
                                        while($resc = mysqli_fetch_row($m)){
                                            echo '<option value="'.$resc[0].'">'.mb_strtoupper($resc[1]).' - '.ucwords(mb_strtolower($resc[2])).'</option>';
                                        } ?>
                                    </select>                               
                                </div>
                                <div class="form-group form-inline col-sm-4" style="margin-left:23px">                                  
                                    <select name="sltBuscar" id="sltBuscar" title="Buscar comprobante" class="select2_single form-control" style="width:250px; ">
                                        <option value="">Buscar Comprobante</option>
                                    </select>
                                </div>
                                    <script type="text/javascript">
                                            $("#sltTipoBuscar").change(function(){
                                                var form_data ={
                                                    estruc:25,
                                                    tipo: $("#sltTipoBuscar").val(),
                                                }
                                                var option = '<option value="">Buscar Comprobante</option>';
                                                $.ajax({
                                                    type:'POST',
                                                    url:'jsonPptal/consultas.php',
                                                    data:form_data,
                                                    success: function(data){
                                                        //console.log(data);
                                                        var option = option+data;
                                                       $("#sltBuscar").html(option);
                                                    }
                                                });
                                            })
                                    </script>
                                    <script type="text/javascript">
                                        $("#sltBuscar").change(function(){
                                            //Variables de envio
                                            var form_data = {
                                                comprobante:$("#sltBuscar").val(),
                                                existente:40
                                            };
                                            //Envio ajax
                                            $.ajax({
                                                type:'POST',
                                                url:'consultasBasicas/consultarNumeros.php',
                                                data:form_data,
                                                success: function(data){
                                                    document.location ='registrar_GF_COMPROBANTE_CONTABLE.php';                                                   
                                                }
                                            });
                                        }); 
                                    </script>
                                </div>
                            <div class="form-group form-inline ">                                
                                <div class="col-sm-offset-8 col-sm-8" style="margin-top:-40px;margin-left:496px;margin-bottom: 0px">
                                    <input type="hidden" name="id" id="id" value="<?php echo $cn[0]; ?>" />
                                    <div class="col-sm-1">                                        
                                        <a id="btnNuevo" onclick="javascript:nuevo();" class="btn sombra btn-primary glyphicon glyphicon-plus" style="width: 40px" title="Ingresar nuevo comprobante"></a>
                                    </div>                                    
                                    <div class="col-sm-1" style="">                                        
                                        <button type="submit" id="btnGuardar" class="btn sombra btn-primary" title="Guardar comprobante"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                        
                                    </div>
                                    <?php if(!empty($idC)){  ?>
                                        <script>
                                            $("#btnGuardar").attr('disabled',true);
                                        </script>
                                    <?php }else{ ?>
                                        <script>
                                            $("#btnGuardar").attr('disabled',false);
                                        </script>
                                    <?php } ?>
                                    <div class="col-sm-1">
                                        <button onclick="javascript:modificarComprobante()" id="btnModificar" 
                                                class="btn sombra btn-primary" title="Modificar comprobante">
                                            <li class="glyphicon glyphicon-pencil"></li>
                                        </button>
                                    </div>
                                    <div class="col-sm-1">
                                        <a class="btn sombra btn-primary" title="Imprimir" id="btnImprimir" 
                                           onclick="return informe();">
                                            <li class="fa fa-file-pdf-o" ></li></a>
                                        
                                    </div>  
                                   <div class="col-sm-1" >
                                    <a class="btn sombra btn-primary" title="Imprimir" id="btnImprimirExcel" onclick="return informeExcel();" >
                                      <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                    </a> <!--Imprimir-->
                                  </div>
                                    <div class="col-sm-1" >
                                        <a class="btn sombra btn-primary" title="Eliminar" id="btnEliminar" onclick="eliminarComprobante()" >
                                            <i class="glyphicon glyphicon-remove" aria-hidden="true"></i>
                                        </a> <!--Eliminar-->
                                    </div>
                                  <?php if(!empty($idC)){  ?>
                                        <script>                                           
                                            $("#btnImprimir").attr('disabled',false);
                                            $("#btnImprimirExcel").attr('disabled',false);
                                            </script>
                                            <?php #Validar Si Hay Cierre 
                                            $c = cierrecnt($idC);
                                            if($c ==1) { ?>
                                            <script>
                                            $("#btnEliminar").attr('disabled',true);
                                            </script>
                                            <?php } else { ?>
                                            <script>
                                            $("#btnEliminar").attr('disabled',false);
                                            </script>
                                            
                                            <?php }?>
                                            
                                            
                                        </script>
                                        <?php }else{ ?>
                                        <script>                                            
                                            $("#btnImprimir").attr('disabled',true);
                                            $("#btnImprimirExcel").attr('disabled',true);
                                            $("#btnEliminar").attr('disabled',true);
                                        </script>
                                        <?php } ?>
                                        <script>
                                            function informe(){
                                                window.open('informes/inf_com_cont.php?idcom=<?php echo md5($idC)  ?>');
                                            }
                                        </script>
                                        <script>
                                        function informeExcel(){
                                                window.open('informes/inf_com_cont_Excel.php?idcom=<?php echo md5($idC)  ?>');
                                            }
                                        </script>
                                    <script>
                                        $("#sltTipoC").change(function(){
                                            var tipocomprobante = $("#sltTipoC").val();
                                            if( tipocomprobante == '""' || tipocomprobante==0){
                                                $("#mdltipocomprobante").modal('show');
                                            }else{
                                               var form_data = {
                                                    is_ajax:1,
                                                    numero:$("#txtNumero").val(),
                                                    tipo:$("#sltTipoC").val()
                                                };
                                                $.ajax({
                                                    type: 'POST',
                                                    url: "consultasComprobanteContable/generarNuevo.php",
                                                    data: form_data,
                                                    success: function (response) {
                                                        console.log(response);
                                                        response = response.replace(' ',"");
                                                        response= $.trim( response );                     
                                                        $("#txtNumero").val(response);
                                                        $("#btnGuardar").attr('disabled',false);
                                                        $("#btnCancelarP").css('display','block');
                                                        $("#btnDetalleComprobanteContable").attr('disabled',false);
                                                    }
                                                }); 
                                            }
                                        });
                                        
                                        function nuevo(){
                                            <?php if(!empty($_SESSION['idNumeroC'])){ ?>
                                            form_data = {
                                                is_ajax:1,                                                
                                                id:<?php echo $_SESSION['idNumeroC']; ?>
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasComprobanteContable/vaciarSesion.php",
                                                data: form_data,
                                                success: function (data) {                                                                                
                                                    document.location ='registrar_GF_COMPROBANTE_CONTABLE.php';                                                   
                                                }
                                            });
                                        <?php } ?>                                                                                        
                                        }
                                        
                                        function cancelarM(){
                                        <?php if(!empty($_SESSION['idNumeroC'])){ ?>
                                            form_data = {
                                                is_ajax:1,
                                                numero:$("#txtNumero").val(),
                                                id:<?php echo $_SESSION['idNumeroC']; ?>
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasComprobanteContable/vaciarSesion.php",
                                                data: form_data,
                                                success: function (data) {                                                                                                        
                                                    window.location.reload();                                                    
                                                }
                                            });
                                        <?php } ?>
                                        }                                                                                
                                        function cancelarN(){
                                            <?php if(!empty($_SESSION['idNumeroC'])){ ?>
                                            form_data = {
                                                is_ajax:1,
                                                numero:$("#txtNumero").val(),
                                                id:<?php echo $_SESSION['idNumeroC']; ?>
                                            };
                                            $.ajax({
                                                type: 'POST',
                                                url: "consultasComprobanteContable/vaciarSesion.php",
                                                data: form_data,
                                                success: function (data) {                                                                                                        
                                                    window.location.reload();                                                    
                                                }
                                            });
                                        <?php } ?>
                                        }
                                    </script>
                                    <script>
                                        function eliminarComprobante(){
                                            var fecha = $("#fecha").val();
                                            var form_data = {case: 4, fecha: fecha};
                                            $.ajax({
                                                type: "POST",
                                                url: "jsonSistema/consultas.php",
                                                data: form_data,
                                                success: function (response)
                                                {
                                                    console.log(response+'cierre');
                                                    if (response == 1) {
                                                        $("#periodoC").modal('show');


                                                    } else {
                                                        var id = $("#id").val();
                                                        var form_data ={estruc:16, id:id};
                                                        $.ajax({
                                                type: 'POST',
                                                url: "jsonPptal/consultas.php",
                                                data: form_data,
                                                success: function (data) {                                                                                                        
                                                    console.log(data);
                                                    if(data==0){
                                                        $("#msj").html("¿Desea Eliminar El Comprobante Seleccionado?");
                                                        $("#mdlMensajes").modal("show");
                                                        $("#btnMensaje1").click(function(){
                                                            $("#mdlMensajes").modal("hide");
                                                            eliminarc();
                                                        });
                                                        $("#btnMensaje2").click(function(){
                                                            $("#mdlMensajes").modal("hide");
                                                        })
                                                        
                                                    }   else {
                                                        if(data==1){
                                                            $("#msj").html("El Periodo Ya Esta Cerrado, No Se Puede Eliminar Comprobante");
                                                            $("#mdlMensajes").modal("show");
                                                            $("#btnMensaje1").click(function(){
                                                                $("#mdlMensajes").modal("hide");
                                                            });
                                                            $("#btnMensaje2").click(function(){
                                                                $("#mdlMensajes").modal("hide");
                                                            })

                                                        } else {
                                                            if(data ==2){
                                                                $("#msj").html("El Comprobante Pertenece a Interfaz de Almacen.<br/> ¿Desea Eliminarlo?");
                                                                $("#mdlMensajes").modal("show");
                                                                $("#btnMensaje1").click(function(){
                                                                    $("#mdlMensajes").modal("hide");
                                                                    eliminarc();
                                                                });
                                                                $("#btnMensaje2").click(function(){
                                                                    $("#mdlMensajes").modal("hide");
                                                                })
                                                            } else {
                                                                if(data==3){
                                                                    $("#msj").html("No Se Puede Eliminar Comprobante, El Comprobante Tiene Cuentas Conciliadas");
                                                                    $("#mdlMensajes").modal("show");
                                                                    $("#btnMensaje1").click(function(){
                                                                        $("#mdlMensajes").modal("hide");
                                                                    });
                                                                    $("#btnMensaje2").click(function(){
                                                                        $("#mdlMensajes").modal("hide");
                                                                    })
                                                                } else {
                                                                    $("#msj").html("No Se Puede Eliminar Comprobante");
                                                                    $("#mdlMensajes").modal("show");
                                                                    $("#btnMensaje1").click(function(){
                                                                        $("#mdlMensajes").modal("hide");
                                                                    });
                                                                    $("#btnMensaje2").click(function(){
                                                                        $("#mdlMensajes").modal("hide");
                                                                    })

                                                                }

                                                            }
                                                        }
                                                    }                                               
                                                }
                                            });
                                                    }
                                                }
                                            })
                                        }
                                    </script>
                                    <script>
                                        function eliminarc(){
                                            form_data ={estruc:17, id:id};
                                            $.ajax({
                                                type: 'POST',
                                                url: "jsonPptal/consultas.php",
                                                data: form_data,
                                                success: function (data) { 
                                                    if(data==1){
                                                       $("#msj").html("Información Eliminada Correctamente");
                                                        $("#mdlMensajes").modal("show");
                                                        $("#btnMensaje1").click(function(){
                                                            $("#mdlMensajes").modal("hide");
                                                            document.location.reload();
                                                        });
                                                        $("#btnMensaje2").click(function(){
                                                            $("#mdlMensajes").modal("hide");
                                                            document.location.reload();
                                                        }) 
                                                    } else {
                                                        $("#msj").html("No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia");
                                                        $("#mdlMensajes").modal("show");
                                                        $("#btnMensaje1").click(function(){
                                                            $("#mdlMensajes").modal("hide");
                                                            document.location.reload();
                                                        });
                                                        $("#btnMensaje2").click(function(){
                                                            $("#mdlMensajes").modal("hide");
                                                            document.location.reload();
                                                        })
                                                    }
                                                }
                                            });
                                        }
                                    </script>
                                    
                                </div>
                            </div>
                        </form>
                    </div>                    
                </div>
                <div class="col-sm-10 text-center " style="margin-top:5px;" align="">                    
                    <div class="client-form" style="margin-left:60px" class="col-sm-12">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarDetalleComprobanteContable.php" style="margin-top:-15px">
                            <div class="col-sm-1" style="margin-right:130px;">
                                <div class="form-group" style="margin-top: 5px;"  align="left">
                                    <input type="hidden" name="txtComprobante" id="txtComprobante" class="hidden" value="<?php echo $idC ?>"/>
                                    <input type="hidden" name="txtDesc" value="<?php echo $descripcion ?>">
                                    <input type="hidden" name="txtFecha" value="<?php echo $fecha ?>">
                                    <input type="hidden" name="formulario" id="formulario" value="registrar_GF_COMPROBANTE_CONTABLE.php">
                                    <?php 
                                    $sqlC = "SELECT id_unico,
                                                codi_cuenta,
                                                nombre 
                                        FROM    gf_cuenta
                                        WHERE   parametrizacionanno = $anno 
                                        AND (movimiento = 1
                                        OR      centrocosto = 1
                                        OR      auxiliartercero = 1
                                        OR      auxiliarproyecto = 1 ) ORDER BY codi_cuenta ASC";
                                    $res = $mysqli->query($sqlC);
                                    ?>
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Cuenta
                                    </label>
                                    <select name="sltcuenta" id="sltcuenta" autofocus="" class="form-control col-sm-1" style="width:200px;height:26px;" title="Seleccione cuenta" required="">
                                        <option value="">Cuenta</option>
                                        <?php 
                                        while ($fila = mysqli_fetch_row($res)){ ?>
                                        <option value="<?php echo $fila[0]; ?>" ><?php echo ucwords( (mb_strtolower($fila[1].' - '.$fila[2]))) ?></option>    
                                        <?php                                         
                                        }
                                        ?>
                                        <script type="text/javascript">
                                            $(document).ready(function(){                                                
                                                var padre = 0;
                                                $("#slttercero").prop('disabled',false);
                                            $("#sltcuenta").change(function(){
                                                if ($("#sltcuenta").val()=="" || $("#sltcuenta").val()==0) {
                                                    padre = 0;         
                                                    $("#slttercero").prop('disabled',false);
                                                }else{
                                                    padre = $("#sltcuenta").val();
                                                }
                                                var form_data = {
                                                    is_ajax:1,
                                                    data:+padre
                                                };                                        
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultasDetalleComprobante/consultarTercero.php",
                                                    data:form_data,                                                    
                                                    success: function (data) {
                                                        var tercero = document.getElementById('slttercero');
                                                        if (data==1) {
                                                            tercero.disabled=false; 
                                                        }else if(data==2){
                                                            $("#slttercero").prop('disabled',false);
                                                        }                                                       
                                                    }
                                                });
                                            });
                                        });
                                        </script>
                                        <script type="text/javascript">
                                            $(document).ready(function(){                                                
                                                var padre = 0;
                                                $("#sltcentroc").prop('disabled',true);
                                            $("#sltcuenta").change(function(){
                                                if ($("#sltcuenta").val()=="" || $("#sltcuenta").val()==0) {
                                                    padre = 0;         
                                                    $("#sltcentroc").prop('disabled',true);
                                                }else{
                                                    padre = $("#sltcuenta").val();
                                                }
                                                var form_data = {
                                                    is_ajax:1,
                                                    data:+padre
                                                };                                        
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultasDetalleComprobante/consultarCentroC.php",
                                                    data:form_data,                                                    
                                                    success: function (data) {
                                                        var centro = document.getElementById('sltcentroc');
                                                        if (data==1) {
                                                            centro.disabled=false; 
                                                        }else if(data==2){
                                                            $("#sltcentroc").prop('disabled',true);
                                                        }                                                       
                                                    }
                                                });
                                            });
                                        });
                                        </script>
                                        <script type="text/javascript">
                                            $(document).ready(function(){                                                
                                                var padre = 0;
                                                $("#sltproyecto").prop('disabled',true);
                                            $("#sltcuenta").change(function(){
                                                if ($("#sltcuenta").val()=="" || $("#sltcuenta").val()==0) {
                                                    padre = 0;         
                                                    $("#sltproyecto").prop('disabled',true);
                                                }else{
                                                    padre = $("#sltcuenta").val();
                                                }
                                                var form_data = {
                                                    is_ajax:1,
                                                    data:+padre
                                                };                                        
                                                $.ajax({
                                                    type:"POST",
                                                    url:"consultasDetalleComprobante/consultaProyecto.php",
                                                    data:form_data,                                                    
                                                    success: function (data) {
                                                        var centro = document.getElementById('sltproyecto');
                                                        if (data==1) {
                                                            centro.disabled=false; 
                                                        }else if(data==2){
                                                            $("#sltproyecto").prop('disabled',true);
                                                        }                                                       
                                                    }
                                                });
                                            });
                                        });
                                        </script>
                                    </select>
                                </div>                               
                            </div>    
                            <div class="col-sm-1" style="margin-right:30px;">
                                <div class="form-group" style="margin-top: 5px;"  align="left">
                                    
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Tercero
                                    </label>
                                    <select name="slttercero" id="slttercero" class="form-control col-sm-1" style="width:100px;height:26px;" title="Seleccione tercero" required="">                                        
                                        
                                        <?php 
                                        if (!empty($_SESSION['idNumeroC'])) {
                                           $sql = "SELECT ter.id_unico, IF(CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos) IS NULL 
                                                                OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                                                (ter.razonsocial),
                                                                CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos  )) AS NOMBRE, 
                                                ter.id_unico, 
                                            ter.numeroidentificacion AS 'TipoD' , ter.digitoverficacion 
                                            FROM gf_comprobante_cnt cn
                                            LEFT JOIN gf_tercero ter ON cn.tercero = ter.id_unico 
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                             WHERE ter.compania = $compania   AND cn.id_unico =".$_SESSION['idNumeroC'];
                                            $rs = $mysqli->query($sql); 
                                            $ter = mysqli_fetch_row($rs);
                                            echo '<option value="'.$ter[0].'">'.$ter[1].' - '.$ter[3].'</option>';
                                        }else {
                                            echo '<option value="2">Tercero</option>';
                                        }
                                        $sql = "SELECT  IF(CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos) IS NULL 
                                                                OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                                                (ter.razonsocial),
                                                                CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos  )) AS NOMBRE, 
                                                ter.id_unico, 
                                            CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD' , ter.digitoverficacion 
                                            FROM gf_tercero ter
                                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                             WHERE ter.compania = $compania   LIMIT 20";
                                        $rs = $mysqli->query($sql);
                                        while($row=  mysqli_fetch_row($rs)){   
                                            if(empty($row[3])) {
                                            echo '<option value='.$row[1].'>'.ucwords(mb_strtolower($row[0].PHP_EOL.$row[2])).'</option>';
                                            } else {
                                                echo '<option value='.$row[1].'>'.ucwords(mb_strtolower($row[0].PHP_EOL.$row[2].'-'.$row[3])).'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-1" style="margin-right:30px;">
                                <div class="form-group" style="margin-top: 5px;"  align="left">
                                    <?php 
                                    $sqlCC = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE nombre = 'varios' AND parametrizacionanno = $anno ORDER BY nombre ASC";
                                    $a = $mysqli->query($sqlCC);
                                    $filaC = mysqli_fetch_row($a);
                                    $sqlCT = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico != $filaC[0] AND parametrizacionanno = $anno ORDER BY nombre ASC";
                                    $r = $mysqli->query($sqlCT);
                                    ?>
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Centro Costo
                                    </label>
                                    <select name="sltcentroc" id="sltcentroc" class="form-control" style="width:100px;height:26px;padding:2px" title="Seleccione centro costo" required="">
                                        <?php 
                                        if(!empty($_SESSION['centrocosto'])){
                                            $cb =$_SESSION['centrocosto'];
                                            $sqlCC = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico = $cb AND parametrizacionanno = $anno ORDER BY nombre ASC";
                                            $a = $mysqli->query($sqlCC);
                                            $filaC = mysqli_fetch_row($a);
                                            $sqlCT = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico != $filaC[0] AND parametrizacionanno = $anno ORDER BY nombre ASC";
                                            $r = $mysqli->query($sqlCT);
                                            while($b = mysqli_fetch_row($r)){
                                                echo '<option value="'.$b[0].'">'.$b[1].'</option>';
                                            }
                                        }else{
                                        ?>
                                            <option value="<?php echo $filaC[0]; ?>"><?php echo ucwords( (mb_strtolower($filaC[1]))); ?></option>
                                            <?php 
                                            while($fila2=  mysqli_fetch_row($r)){ ?>
                                                <option value="<?php echo $fila2[0]; ?>"><?php echo ucwords( (mb_strtolower($fila2[1]))); ?></option>   
                                            <?php                                          
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-1" style="margin-right:30px;">
                                <div class="form-group" style="margin-top: 5px;"  align="left">
                                    
                                    <label class="control-label">
                                        <strong class="obligado">*</strong>Proyecto
                                    </label>
                                    <select name="sltproyecto" id="sltproyecto" class="form-control" style="width:100px;height:26px;padding:2px" title="Seleccione proyecto" required="">
                                        <?php 
                                        if(!empty($_SESSION['proyecto'])){
                                            $ccS = $_SESSION['proyecto'];    
                                            $sql = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico = $ccS";
                                            $fi = $mysqli->query($sql);
                                            $fr = mysqli_fetch_row($fi);
                                            echo '<option value="'.$fr[0].'">'.ucwords(mb_strtolower($fr[1])).'</option>';
                                            $sqli = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico != $fr[0]";
                                            $fa = $mysqli->query($sqli);
                                            while($fe = mysqli_fetch_row($fa)){
                                                echo '<option value="'.$fe[0].'">'.ucwords(mb_strtolower($fe[1])).'</option>';
                                            }
                                        }else{
                                        ?>
                                            <?php 
                                            $sqlP = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE nombre = 'VARIOS'" ;
                                            $d = $mysqli->query($sqlP);                                    
                                            $filaP = mysqli_fetch_row($d);
                                            $sqlPY = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico != $filaP[0]" ;
                                            $X = $mysqli->query($sqlPY);
                                            ?>
                                            <option value="<?php echo $filaP[0]; ?>">Proyecto</option>
                                            <option value="<?php echo $filaP[0]; ?>"><?php echo ucwords( (mb_strtolower($filaP[1]))) ?></option>
                                            <?php                                         
                                            while($fila3 = mysqli_fetch_row($X)){ ?>
                                                <option value="<?php echo $fila3[0]; ?>"><?php echo ucwords( (mb_strtolower($fila3[1]))) ?></option>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-1" >
                                <script type="text/javascript">
                                        $(document).ready(function(){
                                            $("#txtValorD").keyup(function(){
                                                $("#txtValorC").prop('disabled',true);
                                            });
                                            $("#txtValorC").keyup(function(){
                                                $("#txtValorD").prop('disabled',true);
                                            });
                                        });
                                        function justNumbers(e){   
                                            var keynum = window.event ? window.event.keyCode : e.which;
                                            if ((keynum == 8) || (keynum == 46) || (keynum == 45))
                                            return true;
                                            return /\d/.test(String.fromCharCode(keynum));
                                        }
                                    </script>
                                <div class="form-group" style="margin-top: 8px;" align="left">
                                    <label class="control-label">
                                        Valor Débito
                                    </label>
                                    <input type="text" name="txtValorD" onkeypress="return justNumbers(event);" id="txtValorD" maxlength="50" style="height:26px;padding:2px;width:80px"/>                                    
                                </div>
                            </div>
                            <div class="col-sm-1" style="margin-right:40px;" >
                                <div class="form-group" style="margin-top: 8px;" align="left">
                                    <label class="control-label">
                                        Valor Crédito
                                    </label>
                                    <input type="text"  name="txtValorC" onkeypress="return justNumbers(event);" id="txtValorC" maxlength="50" style="height:26px;padding:2px;width:80px"/>
                                </div>
                            </div>
                            <div class="col-sm-1" align="left" style="margin-top:25px;margin-left:-80px;margin-right:30px; ">
                                <button  type="submit" class="btn btn-primary sombra" id="btnDetalleComprobanteContable"><li class="glyphicon glyphicon-floppy-disk"></li></button>                                
                                <input type="hidden" name="MM_insert" >
                            </div>
                            <script type="text/javascript" >
                                var id =<?php echo $idComprobante; ?>;
                                if(id==0){
                                    $("#btnDetalleMovimiento").attr('disabled',true);
                                    $("#btnDetalleComprobanteContable").attr('disabled',true);
                                }else{
                                    $("#btnDetalleMovimiento").attr('disabled',false);
                                    $("#btnDetalleComprobanteContable").attr('disabled',false);
                                }
                            </script>
                        </form>                        
                    </div>
                </div>
                <div class=" contTabla col-sm-8" style="margin-top:-20px">
                    <?php 
                    $sumar = 0;
                    $sumaT = 0;
                    ?>
                    <?php $countd =detallesnumcnt( $idComprobante); 
                    #** Calcular Valores Débito y Créidot 
                    $banco ="SELECT DISTINCT 
                            cn.id_unico,
                            (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND  dc1.valor>0) AS debito1,
                             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND dc1.valor<0 ) AS credito2,
                             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor>0) AS credito, 
                             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor<0) AS debito2  
                        FROM gf_comprobante_cnt cn 
                        WHERE cn.id_unico = $idComprobante";
                    $banco = $mysqli->query($banco);
                    $row = mysqli_fetch_row($banco);
                    $debito1 =$row[1];
                    $debitoN =$row[4]*-1;
                    $credito1 =$row[3];
                    $creditoN =$row[2]*-1;
                    $debito = $debito1+$debitoN;
                    $credito = $credito1+$creditoN;
                    
                    if($countd<=10) { ?>
                    <div class="table-responsive contTabla" >
                        <?php 
                            if (!empty($_SESSION['idNumeroC'])) {
                                
                                $sql="  
                                SELECT
                                   DT.id_unico,
                                   CT.id_unico as cuenta,
                                   CT.nombre,
                                   CT.codi_cuenta,
                                   CT.naturaleza,
                                   N.id_unico,
                                   N.nombre,
                                   IF(CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos) IS NULL 
                                                            OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                                            (ter.razonsocial),
                                                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos  )) AS 'NOMBRE', 
                                                ter.id_unico, 
                                                CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD',                                   
                                   CC.id_unico,
                                   CC.nombre,
                                   PR.id_unico,
                                   PR.nombre,
                                   DT.valor, 
                                   DT.revelacion , DT.conciliado 
                                FROM
                                  gf_detalle_comprobante DT
                                LEFT JOIN
                                  gf_cuenta CT ON DT.cuenta = CT.id_unico
                                LEFT JOIN
                                  gf_naturaleza N ON N.id_unico = DT.naturaleza
                                LEFT JOIN
                                  gf_tercero ter ON DT.tercero = ter.id_unico
                                LEFT JOIN
                                  gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
                                LEFT JOIN
                                  gf_centro_costo CC ON DT.centrocosto = CC.id_unico
                                LEFT JOIN
                                  gf_proyecto PR ON DT.proyecto = PR.id_unico
                                WHERE (DT.comprobante) = $idComprobante";
                                $rs = $mysqli->query($sql);
                            }
                    
                    ?>
                    <input type="hidden" id="idPrevio" value="">
                    <input type="hidden" id="idActual" value="">
                    
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">                            
                        <thead>
                            <tr>
                        <style>
                            .cabeza{
                                width:auto;
                            }
                        </style>
                                <td class="oculto">Identificador</td>
                                <td width="7%" class="cabeza"></td>
                                <td class="cabeza"><strong>Cuenta Contable</strong></td>
                                <td class="cabeza"><strong>Tercero</strong></td>
                                <td class="cabeza"><strong>Centro Costo</strong></td>
                                <td class="cabeza"><strong>Proyecto</strong></td>
                                <td class="cabeza"><strong>Débito</strong></td>
                                <td class="cabeza"><strong>Crédito</strong></td>                                
                                <td class="cabeza"><strong>Documentos</strong></td>
                                <td class="cabeza"><strong>Revelación</strong></td>
                            </tr>
                            <tr>
                                <th class="oculto">Identificador</th>
                                <th width="7%"></th>
                                <th class="cabeza">Cuenta Contable</th>
                                <th class="cabeza">Tercero</th>
                                <th class="cabeza">Centro Costo</th>
                                <th class="cabeza">Proyecto</th>
                                <th class="cabeza">Débito</th>
                                <th class="cabeza">Crédito</th>
                                <th class="cabeza">Documentos</th>
                                <th class="cabeza">Revelación</th>
                            </tr>
                        </thead>
                        <tbody>  
                            <?php 
                            while ($row = mysqli_fetch_row($rs)) { ?>
                            <tr>
                                <td class="campos oculto">
                                    <?php echo $row[0]; ?>
                                </td>
                                <td class="campos">
                                    <?php if(!empty($_SESSION['idNumeroC'])){
                                        $cierre = cierrecnt($_SESSION['idNumeroC']);
                                        if($cierre ==0){ 
                                            if($row[16]==1){} else { ?>
                                    
                                    <a href="#<?php echo $row[0];?>" onclick="javascript:eliminar(<?php echo $row[0]; ?>)" title="Eliminar">
                                        <li class="glyphicon glyphicon-trash"></li>
                                    </a>
                                    <a href="#<?php echo $row[0];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[0]; ?>);javascript:cargarT(<?php echo $row[0]; ?>);javascript:cargarT2(<?php echo $row[0]; ?>);javascript:cargarCentro(<?php echo $row[0]; ?>);javascript:cargarCentro2(<?php echo $row[0]; ?>);javascript:cargarProyecto(<?php echo $row[0]; ?>);javascript:cargarProyecto2(<?php echo $row[0]; ?>)">
                                        <li class="glyphicon glyphicon-edit"></li>
                                    </a> 
                                    <?php } } }?>
                                </td>
                                <!-- Código de cuenta y nombre de la cuenta -->
                                <td class="campos text-left" >
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="cuenta'.$row[0].'">'. (ucwords(mb_strtolower($row[3].' - '.$row[2]))).'</label>'; ?>
                                    <div id="sltCDiv<?php echo $row[0]; ?>" style="display: none;">
                                    <select  class="select2_single col-sm-12 campoD" id="sltC<?php echo $row[0]; ?>" style="padding: 2px;height:18; ">
                                        <option value="<?php echo $row[1];?>"><?php echo $row[3].'-'.$row[2]; ?></option>
                                            <?php 
                                            $sqlCTN = "SELECT DISTINCT id_unico,codi_cuenta,nombre FROM gf_cuenta "
                                                    . "WHERE (codi_cuenta != $row[3]) AND parametrizacionanno = $anno AND (movimiento = 1 
                                                    OR      centrocosto = 1
                                                    OR      auxiliartercero = 1
                                                    OR      auxiliarproyecto = 1) ORDER BY codi_cuenta ASC";
                                            $result = $mysqli->query($sqlCTN);
                                            while ($s = mysqli_fetch_row($result)){
                                                echo '<option value="'.$s[0].'">'.$s[1].' - '.$s[2].'</option>';
                                            }
                                            ?>                                                
                                    </select>
                                    </div>
                                </td>
                                <!-- Datos de tercero -->
                                <td class="campos text-left">
                                    <?php echo '<label class="valorLabel" title="'.$row[9].'" style="font-weight:normal" id="tercero'.$row[0].'">'. (ucwords(mb_strtolower($row[7]))).'</label>'; ?>
                                    <div id="sltTercero<?php echo $row[0]; ?>" style="display: none;">
                                        <select id="sltTerceroV<?php echo $row[0]; ?>" style="padding: 2px;height:18;" class="col-sm-12 campoD" onclick="cargarT(<?=$row[0]?>)">
                                        <option value="<?php echo $row[8] ?>"><?php echo  (ucwords(mb_strtolower($row[7]))) ?></option>
                                        <?php
                                        $sqlTR = "SELECT  IF(CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos) IS NULL 
                                                            OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                                            (ter.razonsocial),
                                                            CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos  )) AS NOMBRE, 
                                                ter.id_unico, CONCAT(ter.numeroidentificacion) AS 'TipoD' , ter.digitoverficacion FROM gf_tercero ter
                                                LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                                                WHERE  ter.id_unico != $row[8] AND ter.compania = $compania ORDER BY NOMBRE ASC LIMIT 20 ";
                                        $resulta = $mysqli->query($sqlTR);
                                        while($e = mysqli_fetch_row($resulta)){  
                                            if(empty($e[3])) { 
                                            echo '<option value="'.$e[1].'">'.ucwords(mb_strtolower($e[0].' - '.$e[2])).'</option>';                                                  
                                            } else {
                                                echo '<option value="'.$e[1].'">'.ucwords(mb_strtolower($e[0].' - '.$e[2].'-'.$e[3])).'</option>';                                                  
                                            }
                                        }
                                        ?>
                                    </select>
                                    </div>
                                </td>
                                <td class="campos text-left">
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="centroC'.$row[0].'">'. (ucwords(mb_strtolower($row[11]))).'</label>'; ?>
                                    <div id="sltcentroCDiv<?php echo $row[0]; ?>" style="display: none;">
                                    <select id="sltcentroC<?php echo $row[0]; ?>" style="padding:2px;height:19px" class=" select2_single col-sm-12 campoD">
                                        <option value="<?php echo $row[10]; ?>"><?php echo $row[11]; ?></option>
                                        <?php
                                        $sqlCCT = "SELECT DISTINCT id_unico,nombre FROM gf_centro_costo WHERE id_unico != '$row[10]' AND parametrizacionanno = $anno";
                                        $g = $mysqli->query($sqlCCT);
                                        while($f = mysqli_fetch_row($g)){
                                            echo '<option value="'.$f[0].'">'.$f[1].'</option>';
                                        }
                                        ?> 
                                    </select>
                                    </div>
                                </td>
                                <td class="campos text-left">
                                    <?php echo '<label class="valorLabel" style="font-weight:normal" id="proyecto'.$row[0].'">'. (ucwords(mb_strtolower($row[13]))).'</label>'; ?>
                                    <div id="sltProyectoDiv<?php echo $row[0]; ?>" style="display: none;">
                                    <select style="padding:2px;height:19px" class="select2_single col-sm-12 campoD" id="sltProyecto<?php echo $row[0]; ?>">
                                        <option value="<?php echo $row[12]; ?>"><?php echo $row[13]; ?></option>
                                        <?php 
                                        $sqlCP = "SELECT DISTINCT id_unico,nombre FROM gf_proyecto WHERE id_unico != $row[17]";
                                        $result = $mysqli->query($sqlCP);
                                        while ($y = mysqli_fetch_row($result)){
                                            echo '<option value="'.$y[0].'">'.$y[1].'</option>';
                                        }
                                        ?>
                                        <!-- Validación de campos en la tabla -->                                                                                                                                              
                                    </select>
                                    </div>
                                </td>
                                <!-- Campo de valor debito y credito. Validación para imprimir valor -->
                                <td class="campos text-right" align="center">

                                    <?php 

                                    if ($row[4] == 1) {
                                        if($row[14] >= 0){
                                            $sumar += $row[14];
                                            echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">'.number_format($row[14], 2, '.', ',').'</label>';
                                            echo '<input maxlength="50" align="center" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px;" class="col-sm-12 text-left campoD" type="text" name="txtDebito'.$row[0].'" id="txtDebito'.$row[0].'" value="'.$row[14].'" />';
                                        }else{
                                            echo '<label style="font-weight:normal" id="debitoP'.$row[0].'">0</label>';
                                            echo '<input maxlength="50" type="text" onkeypress="return justNumbers(event)" align="center" style="display:none;padding:2px;height:19px;" class="col-sm-12 campoD text-left" name="txtDebito'.$row[0].'"  id="txtDebito'.$row[0].'" value="0"/>';
                                        }  
                                    }else if($row[4] == 2){
                                        if($row[14] <= 0){
                                            $x = (float) substr($row[14],'1');
                                            $sumar += $x;
                                            echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">'.number_format($x, 2,'.', ',').'</label>';
                                            echo '<input maxlength="50" align="center" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px;" class="col-sm-12 campoD text-left" type="text" name="txtDebito'.$row[0].'" id="txtDebito'.$row[0].'" value="'.$x.'" />';
                                        }else{
                                            echo '<label class="valorLabel" style="font-weight:normal" id="debitoP'.$row[0].'">0</label>';
                                            echo '<input maxlength="50" align="center" onkeypress="return justNumbers(event)" type="text" style="display:none;padding:2px;height:19px;" class="col-sm-12 campoD text-left" name="txtDebito'.$row[0].'"  id="txtDebito'.$row[0].'" value="0"/>';
                                        }
                                    }

                                   ?>                                            
                                </td>
                                <td class="campos text-right">
                                    <?php
                                    if ($row[4] == 2) {
                                        if($row[14] >= 0){
                                            $sumaT += $row[14];
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">'.number_format($row[14], 2, '.', ',').'</label>';
                                            echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  type="text" name="txtCredito'.$row[0].'" id="txtCredito'.$row[0].'" value="'.$row[14].'" />';                                                                                                
                                        }else{
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">0</label>';
                                            echo '<input maxlength="50" type="text" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px" class="col-sm-12 campoD text-left"  name="txtCredito'.$row[0].'"  id="txtCredito'.$row[0].'" value="0"/>';
                                        }
                                    }else if($row[4] == 1){
                                       if($row[14] <= 0){
                                            $x = (float) substr($row[14],'1');
                                            $sumaT += $x;
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">'.number_format($x, 2, '.', ',').'</label>';
                                            echo '<input maxlength="50" onkeypress="return justNumbers(event)" style="display:none;padding:2px;height:19px;" class="col-sm-12 text-left campoD"  type="text" name="txtCredito'.$row[0].'" id="txtCredito'.$row[0].'" value="'.$x.'" />';                                                                                                
                                    }else{
                                            echo '<label class="valorLabel" style="font-weight:normal" id="creditoP'.$row[0].'">0</label>';
                                            echo '<input maxlength="50" type="text" onkeypress="return justNumbers(event)" class="col-sm-12 text-left campoD" style="display:none;padding:2px;height:19px" name="txtCredito'.$row[0].'" id="txtCredito'.$row[0].'" value="0"/>';
                                        }
                                    }?>                                    
                                </td>
                                <td class="campos text-center">
                                   
                                    <div style="display:inline">
                                        <table id="tab<?php echo $row[0] ?>" style="padding:0px;background-color:transparent;background:transparent;">
                                                <tbody>
                                                    <tr style="background-color:transparent;">
                                                        <td style="background-color:transparent;">
                                                            <a  href="#<?php echo $row[0];?>" title="Guardar" id="guardar<?php echo $row[0]; ?>" style="display: none;" onclick="javascript:guardarCambios(<?php echo $row[0]; ?>)">
                                                                <li class="glyphicon glyphicon-floppy-disk"></li>
                                                            </a>
                                                        </td>
                                                        <td style="background-color:transparent;">
                                                            <a href="#<?php echo $row[0];?>" title="Cancelar" id="cancelar<?php echo $row[0] ?>" style="display: none" onclick="javascript:cancelar(<?php echo $row[0];?>)" >
                                                                <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                        </table>
                                    </div>
                                    <div style="display:inline">
                                        <a id="btnDetalleMovimiento" onclick="javascript:abrirdetalleMov(<?php echo $row[0]?>,<?php echo $row[14]?>);" title="Comprobante detalle movimiento"><i class="glyphicon glyphicon-file"></i></a>                                        
                                        
                                    </div>
                                    
                                </td>
                                <td class="campos text-center">
                                    <?php if(empty($row[15])) { ?>
                                    <div style="display:inline">
                                            <a id="btnDRevelaciones" onclick="javascript:revelaciones(<?php echo $row[0]?>);" title="Revelación"><i class="glyphicon glyphicon-paste"></i></a>                                        
                                    </div> 
                                    <?php } else { ?>
                                        <div style="display:inline">
                                            <a id="btnDRevelaciones" onclick="javascript:verRevelaciones(<?php echo $row[0].','."'".$row[15]."'"?>);" title="Revelación"><i class="glyphicon glyphicon-eye-open"></i></a>                                        
                                        </div>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php }
                            ?>
                        </tbody>
                    </table>
                    </div> 
                    <?php } ?>
                    <script type="text/javascript" >
                        function abrirdetalleMov(id,valor){                                                                                                   
                            var form_data={                            
                            id:id,
                            valor:valor
                            };
                            $.ajax({
                                type: 'POST',
                                url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO.php#mdlDetalleMovimiento",
                                data:form_data,
                                success: function (data) { 
                                    $("#mdlDetalleMovimiento").html(data);
                                    $(".mov").modal('show');
                                }
                            });

                        }                                                                                        
                    </script>
                    
                    <?php 
                    $valorD = $debito;
                    $valorC = $credito;
                    #Diferencia
                    $diferencia = $valorD - $valorC;
                    ?>
                    <style>
                        .valores:hover{
                            cursor: pointer;
                            color:#1155CC;
                        }
                    </style>
                    <div class="col-sm-offset-6  col-sm-6 text-left">
                        <div class="col-sm-2">
                            <div class="form-group" style="margin-top:5px;margin-bottom:-10px" align="left">                                    
                                <label class="control-label">
                                    <strong>Totales:</strong>
                                </label>                                
                            </div>
                        </div>                        
                        <div class="col-sm-2 text-right" style="margin-top:5px;" align="left">
                            <?php 
                            if (($valorD) === NULL) { ?>
                                 <label class="control-label valores" title="Suma débito">0</label>                   
                            <?php
                            }else { ?>
                                 <label class="control-label valores" title="Suma débito"><?php echo number_format($valorD, 2, '.', ',') ?></label>
                            <?php }
                            ?>
                        </div>                        
                        <div class="col-sm-2 text-right col-sm-offset-1" style="margin-top:5px;" align="left">
                            <?php 
                            if ($valorC === NULL) { ?>
                                <label class="control-label valores" title="Suma crédito">0</label>
                            <?php
                            }else{ ?>
                                <label class="control-label valores" title="Suma crédito"><?php echo number_format($valorC, 2, '.', ','); ?></label>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="col-sm-2 text-right" style="margin-top:5px;" align="left">
                            <?php 
                            if ($diferencia === 0) { ?>
                                  <label class="control-label text-right valores" title="Diferencia">0.00</label>                          
                            <?php }else{ ?>
                                  <label class="control-label text-right valores" title="Diferencia"><?php echo number_format($diferencia, 2, '.', ',') ; ?></label>
                            <?php    
                            }
                            ?>                                  
                        </div> 
                    </div>                                       
                </div>
                <div class="col-sm-8 col-sm-1" style="margin-top: -360px"  >
                        <table class="tablaC table-condensed text-center" align="center">
                            <thead>
                                <tr>
                                    <tr>                                        
                                        <th>
                                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                        </th>
                                    </tr>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GF_CUENTA_P.php">CUENTA</a>
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>
                                        <!-- onclick="return ventanaSecundaria('registrar_GF_DESTINO.php')" -->
                                        <a onclick="javascript:abrirMT()" class="btn btn-primary btnInfo">PERSONA</a>                                       
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_CENTRO_COSTO.php">CENTRO COSTO</a>
                                    </td>
                                </tr>                               
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GF_PROYECTO.php">PROYECTO</a>
                                    </td>
                                </tr>
                                <?php 
                                $count =detallesnumcnt( $idComprobante);
                                if($count<=0 ) {  ?>
                                <tr>                                    
                                    <td>
                                        <button id="btnCopiar" class="btn btn-primary btnInfo" onclick="copiar()">COPIAR</button>
                                    </td>
                                </tr>    
                                <?php } 
                                IF($count>10){ ?>
                                    <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="GF_DETALLES_COMPROBANTE_CNT.php?id=<?php echo md5($idComprobante)?>&t=1">VER DETALLES</a>
                                    </td>
                                    </tr>
                                <?php }  ?>  
                            </tbody>
                        </table>
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
                        <p>¿Desea eliminar el registro seleccionado de Detalle Comprobante?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mdltipocomprobante" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Seleccione un tipo de comprobante.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="tbmtipoF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                        <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="infoM" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información modificada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="noModifico" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">          
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se ha podido modificar la información.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modales de guardado -->
        <div class="modal fade" id="mdlGuardado" role="dialog" align="center" >
            <div class="modal-dialog">
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>Información guardada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnGuardado" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
              </div>
            </div>
          </div>
          <div class="modal fade" id="mdlNoGuardado" role="dialog" align="center" >
            <div class="modal-dialog">
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>No se ha podido guardar la información.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnGuardado2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
              </div>
            </div>
          </div> 
            <script type="text/javascript" >
                function revelaciones(id){                                                                                                   
                    $("#iddetalle").val(id);
                    $("#myModalRevelacion").modal('show');
                }                                                                                        
            </script>
             <!--  Modal revelaciones  -->  
            <div class="modal fade" id="myModalRevelacion" role="dialog" align="center" >
              <div class="modal-dialog">
                <div class="modal-content client-form1">
                  <div id="forma-modal" class="modal-header">       
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Revelación</h4>
                  </div>
                  <div class="modal-body ">
                      <form  name="formRev" id="formRev" method="POST" action="javascript:modificarRevelacion()">
                        <input type="hidden" name="iddetalle" id="iddetalle">
                        <div class="form-group" style="margin-top: 13px;">
                            <div>
                            <label for="cuenta2m" class="control-label" style="width: 150px"><strong style="color:#03C1FB;">*</strong>Revelación:</label>
                            </div>
                            <textarea name="revelacion" id="revelacion" required="required" style="width: 300px; height: 80px"></textarea>
                        </div>
                  </div>

                  <div id="forma-modal" class="modal-footer">
                      <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
                    <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
                  </div>
                  </form>
                </div>
              </div>
            </div>
             <script type="text/javascript" >
                function verRevelaciones(id, revelacion){                                                                                                   
                    $("#iddetalleV").val(id);
                    $("#revelacionV").val(revelacion);
                    $("#myModalVerRevelacion").modal('show');
                }                                                                                        
            </script>
             <!--  Modal revelaciones  -->  
            <div class="modal fade" id="myModalVerRevelacion" role="dialog" align="center" >
              <div class="modal-dialog">
                <div class="modal-content client-form1">
                  <div id="forma-modal" class="modal-header">       
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Revelación</h4>
                  </div>
                  <div class="modal-body ">
                      <form  name="formVerRev" id="formVerRev" method="POST" action="javascript:modificarRevelacionVer()">
                        <input type="hidden" name="iddetalleV" id="iddetalleV">
                        <div class="form-group" style="margin-top: 13px;">
                            <div>
                            <label for="cuenta2m" class="control-label" style="width: 150px"><strong style="color:#03C1FB;">*</strong>Revelación:</label>
                            </div>
                            <textarea name="revelacionV" id="revelacionV" required="required" style="width: 300px; height: 80px"></textarea>
                        </div>
                  </div>

                  <div id="forma-modal" class="modal-footer">
                      <button type="submit" class="btn" style="color: #000; margin-top: 2px">Modificar</button>
                    <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
                  </div>
                  </form>
                </div>
              </div>
            </div>
             
             <script>
                 function modificarRevelacion(){
                     var formData = new FormData($("#formRev")[0]);  
                    $.ajax({

                      type:"POST",
                      url:"json/modificar_GF_DETALLE_REVELACIONJson.php",
                      data:formData,
                      contentType: false,
                       processData: false,
                      success: function (data) {
                        result = JSON.parse(data);
                        if(result==true){
                          $("#myModalRevelacion").modal('hide');
                          $("#mdlGuardarRevelacion").modal('show');
                          $("#btnGuardadoRevelacion").click(function(){

                            $("#mdlGuardarRevelacion").modal('hide');
                            document.location.reload();

                          });
                        }else{
                           $("#myModalRevelacion").modal('hide'); 
                          $("#mdlGuardarRevelacionNo").modal('show');
                          $("#btnGuardadoRevelacionNo").click(function(){

                            $("#mdlGuardarRevelacionNo").modal('hide');
                            document.location.reload();

                          });

                        }
                      }
                    });
                 }
             </script>
             <script>
                 function modificarRevelacionVer(){
                     var formData = new FormData($("#formVerRev")[0]);  
                    $.ajax({

                      type:"POST",
                      url:"json/modificar_GF_DETALLE_REVELACIONVERJson.php",
                      data:formData,
                      contentType: false,
                       processData: false,
                      success: function (data) {
                        result = JSON.parse(data);
                        if(result==true){
                          $("#myModalRevelacionVer").modal('hide');
                          $("#mdlGuardarRevelacionVer").modal('show');
                          $("#btnGuardadoRevelacionVer").click(function(){

                            $("#mdlGuardarRevelacionVer").modal('hide');
                            document.location.reload();

                          });
                        }else{
                           $("#myModalRevelacionVer").modal('hide'); 
                          $("#mdlGuardarRevelacionVerNo").modal('show');
                          $("#btnGuardadoRevelacionVerNo").click(function(){

                            $("#mdlGuardarRevelacionVerNo").modal('hide');
                            document.location.reload();

                          });

                        }
                      }
                    });
                 }
             </script>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
       <div class="modal fade" id="mdlGuardarRevelacion" role="dialog" align="center" >
            <div class="modal-dialog">
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>Información guardada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnGuardadoRevelacion" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
              </div>
            </div>
          </div>
             <div class="modal fade" id="mdlGuardarRevelacionNo" role="dialog" align="center" >
            <div class="modal-dialog">
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>La información no se ha podido guardar.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnGuardadoRevelacionNo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
              </div>
            </div>
          </div> 
        <div class="modal fade" id="mdlGuardarRevelacionVer" role="dialog" align="center" >
            <div class="modal-dialog">
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>Información modificada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnGuardadoRevelacionVer" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
              </div>
            </div>
          </div>
             <div class="modal fade" id="mdlGuardarRevelacionVerNo" role="dialog" align="center" >
            <div class="modal-dialog">
              <div class="modal-content">
                <div id="forma-modal" class="modal-header">

                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                  <p>La información no se ha podido guardar.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                  <button type="button" id="btnGuardadoRevelacionVerNo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
              </div>
            </div>
          </div> 
        <script type="text/javascript">           
            function eliminar(id){
                var result = '';
                $("#myModal").modal('show');
                $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminarDetalleComprobanteJson.php?id="+id,
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
        function cargarT2(id){
            
            var padre = 0;
            $("#sltC"+id).change(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
            
                }else{
                    padre = $("#sltC"+id).val();
                }

                var form_data = {
                    is_ajax:1,
                    data:+padre
                };

                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultarTercero.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var tercero = document.getElementById('sltTercero'+id);
                        if (data==1) {
                             tercero.disabled=false;
                        }else if(data==2){
                            
                        }                                                       
                    }
                });
            });
        }
        function cargarT(id){
          
            var padre = 0;
            $("#sltC"+id).append(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    
                }else{
                    padre = $("#sltC"+id).val();
                }

                var form_data = {
                    is_ajax:1,
                    data:+padre
                };

                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultarTercero.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var tercero = document.getElementById('sltTercero'+id);
                        if (data==1) {
                             tercero.disabled=false;
                        }else if(data==2){
                           
                        }                                                       
                    }
                });
            });
        }
        
        function cargarCentro(id){
            
            var padre = 0;
            $("#sltC"+id).append(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
            
                }else{
                    padre = $("#sltC"+id).val();
                }
                var form_data = {
                    is_ajax:1,
                    data:+padre
                };                                        
                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultarCentroC.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var centro = document.getElementById('sltcentroC'+id);
                        if (data==1) {
                            centro.disabled=false; 
                        }else if(data==2){
                            centro.disabled=true; 
                        }                                                       
                    }
                });
            });
        }
        
        function cargarCentro2(id){
            
            var padre = 0;
            $("#sltC"+id).append(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    
                }else{
                    padre = $("#sltC"+id).val();
                }
                var form_data = {
                    is_ajax:1,
                    data:+padre
                };                                        
                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultarCentroC.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var centro = document.getElementById('sltcentroC'+id);
                        if (data==1) {
                            centro.disabled=false; 
                        }else if(data==2){
                            centro.disabled=true; 
                        }                                                       
                    }
                });
            });
        }
        
        function cargarProyecto(id){
            var padre = 0;
            $("#sltProyecto"+id).prop('disabled',true);
            $("#sltC"+id).append(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    $("#sltProyecto"+id).prop('disabled',true);
                }else{
                    padre = $("#sltC"+id).val();
                }
                var form_data = {
                    is_ajax:1,
                    data:+padre
                };                                        
                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultaProyecto.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var proyecto = document.getElementById('sltProyecto'+id);
                        if (data==1) {
                            proyecto.disabled=false; 
                        }else if(data==2){
                            $("#sltProyecto"+id).prop('disabled',true);
                        }                                                       
                    }
                });
            });
        }
        function cargarProyecto2(id){
            var padre = 0;
            $("#sltProyecto"+id).prop('disabled',true);
            $("#sltC"+id).change(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    $("#sltProyecto"+id).prop('disabled',true);
                }else{
                    padre = $("#sltC"+id).val();
                }
                var form_data = {
                    is_ajax:1,
                    data:+padre
                };                                        
                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultaProyecto.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var proyecto = document.getElementById('sltProyecto'+id);
                        if (data==1) {
                            proyecto.disabled=false; 
                        }else if(data==2){
                            $("#sltProyecto"+id).prop('disabled',true);
                        }                                                       
                    }
                });
            });
        }
        </script>     
        
         <script>
            $("#fecha").change(function(){
                
                var tipComPal = $("#sltTipoC").val();
                if(tipComPal==""){
                    $("#fecha").val('');
                    $("#mdltipocomprobante").modal('show');
                } else {
                    //VALIDAR SI YA TUVO CIERRE LA FECHA
                    var fecha = $("#fecha").val();
                    var form_data = { case: 4, fecha: fecha };

                    $.ajax({
                    type: "POST",
                    url: "jsonSistema/consultas.php",
                    data: form_data,
                    success: function(response)
                    {
                        console.log(response+'Cierre');
                        if(response == 1){
                            $("#fecha").val('');
                            $("#periodoC").modal('show');
                        } else {

                         // fecha1();
                        }
                    }
                  }); 
                }
            })
        </script>
        <div class="modal fade" id="periodoC" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Periodo ya ha sido cerrado</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="periodoCA" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script>
             $("#periodoCA").click(function(){
                 $("#fecha").val("");
             })
        </script>        
        <script>
            function fecha1(){
                var tipComPal = $("#sltTipoC").val();
                var fecha = $("#fecha").val();
                var num = $("#txtNumero").val();
                <?php if(!empty($_SESSION['idNumeroC'])) { ?>
                var idComPptal = $("#id").val();
                
                var form_data = { estruc: 24, tipComPal: tipComPal, fecha: fecha, num:num,idComPptal:idComPptal };
                <?php } else {  ?>
                    
                    var form_data = { estruc: 23, tipComPal: tipComPal, fecha: fecha, num:num };
                <?php } ?>
                $.ajax({
                type: "POST",
                url: "jsonPptal/validarFechas.php",
                data: form_data,
                success: function(response)
                {
                    console.log(response); 
                    if(response == 1){
                        $("#myModalAlertErrFec").modal('show');
                    } else {
                      

                    }
                }
              }); 
            }
        </script> 
        <div class="modal fade" id="myModalAlertErrFec" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Fecha Inválida. Verifique nuevamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="AceptErrFec" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
        $('#AceptErrFec').click(function()
        {
           $("#fecha").val("");
           
        });
        </script>
        <script>
            function modificarComprobante(){
                var id = $("#id").val();
                var fecha = $("#fecha").val();
                var tipoComprobante = $("#sltTipoC").val();
                var numeroComprobante = $("#txtNumero").val();
                var tercero = $("#sltTercero").val();
                var centroCosto = $("#sltCentroC").val();
                var proyecto = $("#sltProyecto").val();
                var claseContrato = $("#sltClaseCT").val();
                var numeroContrato = $("#txtNumeroCT").val();
                var estado = $("#txtEstado").val();
                var descripcion = $("#txtDescripcion").val();
                
                var form_data = {
                    is_ajax:1,
                    id:id,
                    fecha:fecha,
                    tipoCmbnt:tipoComprobante,
                    numCmbnt:numeroComprobante,
                    tercero:tercero,
                    centroC:centroCosto,
                    proycto:proyecto,
                    claseCC:claseContrato,
                    numCont:numeroContrato,
                    estado:estado,
                    descpt:descripcion
                };
                
                var result = ' ';
                $.ajax({
                    type: 'POST',
                    url: "json/modificarComprobanteContable.php",
                    data: form_data,
                    success: function (data) {
                        console.log(data);
                        result = JSON.parse(data);
                        
                        if (result==true) {
                            $("#infoM").modal('show');
                        }else{
                            $("#noModifico").modal('show');
                        }
                    }
                });
            }
        </script>
        <script type="text/javascript">
            function modificar(id){
                if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
                    var sltcuentaC = 'sltCDiv'+$("#idPrevio").val();
                    var lblCuentaC = 'cuenta'+$("#idPrevio").val();
                    var sltTerceroC = 'sltTercero'+$("#idPrevio").val();
                    var lblTerceroC = 'tercero'+$("#idPrevio").val();
                    var sltCentroCC = 'sltcentroCDiv'+$("#idPrevio").val();
                    var lblCentroCC = 'centroC'+$("#idPrevio").val();
                    var sltProyectoC = 'sltProyectoDiv'+$("#idPrevio").val();
                    var lblProyectoC = 'proyecto'+$("#idPrevio").val();
                    var txtDebitoC = 'txtDebito'+$("#idPrevio").val();
                    var lblDebitoC = 'debitoP'+$("#idPrevio").val();
                    var txtCreditoC = 'txtCredito'+$("#idPrevio").val();
                    var lblCreditoC = 'creditoP'+$("#idPrevio").val();
                    var guardarC = 'guardar'+$("#idPrevio").val();
                    var cancelarC = 'cancelar'+$("#idPrevio").val();
                    var tablaC = 'tab'+$("#idPrevio").val();
                    
                    $("#"+sltcuentaC).css('display','none');                               
                    $("#"+lblCuentaC).css('display','block');
                    $("#"+sltTerceroC).css('display','none');
                    $("#"+lblTerceroC).css('display','block');
                    $("#"+sltCentroCC).css('display','none');
                    $("#"+lblCentroCC).css('display','block');
                    $("#"+sltProyectoC).css('display','none');
                    $("#"+lblProyectoC).css('display','block');
                    $("#"+txtDebitoC).css('display','none');
                    $("#"+lblDebitoC).css('display','block');
                    $("#"+txtCreditoC).css('display','none');
                    $("#"+lblCreditoC).css('display','block');                
                    $("#"+guardarC).css('display','none');
                    $("#"+cancelarC).css('display','none');
                    $("#"+tablaC).css('display','none');
                }
                
                var sltcuenta = 'sltCDiv'+id;
                var lblCuenta = 'cuenta'+id;
                var sltTercero = 'sltTercero'+id;
                var lblTercero = 'tercero'+id;
                var sltCentroC = 'sltcentroCDiv'+id;
                var lblCentroC = 'centroC'+id;
                var sltProyecto = 'sltProyectoDiv'+id;
                var lblProyecto = 'proyecto'+id;
                var txtDebito = 'txtDebito'+id;
                var lblDebito = 'debitoP'+id;
                var txtCredito = 'txtCredito'+id;
                var lblCredito = 'creditoP'+id;
                var guardar = 'guardar'+id;
                var cancelar = 'cancelar'+id;
                var tabla = 'tab'+id;
                
                $("#"+sltcuenta).css('display','block');                               
                $("#"+lblCuenta).css('display','none');
                $("#"+sltTercero).css('display','block');
                $("#"+lblTercero).css('display','none');
                $("#"+sltCentroC).css('display','block');
                $("#"+lblCentroC).css('display','none');
                $("#"+sltProyecto).css('display','block');
                $("#"+lblProyecto).css('display','none');
                $("#"+txtDebito).css('display','block');
                $("#"+lblDebito).css('display','none');
                $("#"+txtCredito).css('display','block');
                $("#"+lblCredito).css('display','none');                
                $("#"+guardar).css('display','block');
                $("#"+cancelar).css('display','block');
                $("#"+tabla).css('display','inline');
                $("#idActual").val(id);
                if($("#idPrevio").val() != id){
                    $("#idPrevio").val(id);   
                }
               }
        </script>
        <script type="text/javascript">
            function cancelar(id){
                var sltcuenta = 'sltCDiv'+id;
                var lblCuenta = 'cuenta'+id;
                var sltTercero = 'sltTercero'+id;
                var lblTercero = 'tercero'+id;
                var sltCentroC = 'sltcentroCDiv'+id;
                var lblCentroC = 'centroC'+id;
                var sltProyecto = 'sltProyectoDiv'+id;
                var lblProyecto = 'proyecto'+id;
                var txtDebito = 'txtDebito'+id;
                var lblDebito = 'debitoP'+id;
                var txtCredito = 'txtCredito'+id;
                var lblCredito = 'creditoP'+id;
                var guardar = 'guardar'+id;
                var cancelar = 'cancelar'+id;
                var tabla = 'tab'+id;
                
                $("#"+sltcuenta).css('display','none');                               
                $("#"+lblCuenta).css('display','block');
                $("#"+sltTercero).css('display','none');
                $("#"+lblTercero).css('display','block');
                $("#"+sltCentroC).css('display','none');
                $("#"+lblCentroC).css('display','block');
                $("#"+sltProyecto).css('display','none');
                $("#"+lblProyecto).css('display','block');
                $("#"+txtDebito).css('display','none');
                $("#"+lblDebito).css('display','block');
                $("#"+txtCredito).css('display','none');
                $("#"+lblCredito).css('display','block');                
                $("#"+guardar).css('display','none');
                $("#"+cancelar).css('display','none');
                $("#"+tabla).css('display','none');
            }
        </script>
        <script type="text/javascript">
            function guardarCambios(id){
                var sltcuenta = 'sltC'+id;
                var sltTercero = 'sltTerceroV'+id;
                var sltCentroC = 'sltcentroC'+id;
                var sltProyecto = 'sltProyecto'+id;
                var txtDebito = 'txtDebito'+id;
                var txtCredito = 'txtCredito'+id;
                
                var form_data = {
                    is_ajax:1,
                    id:+id,
                    cuenta:$("#"+sltcuenta).val(),
                    tercero:$("#"+sltTercero).val(),
                    centroC:$("#"+sltCentroC).val(),
                    proyecto:$("#"+sltProyecto).val(),
                    debito:$("#"+txtDebito).val(),
                    credito:$("#"+txtCredito).val()
                };
                var result='';
                $.ajax({
                    type: 'POST',
                    url: "json/modificarDetalleComprobante.php",
                    data:form_data,
                    success: function (data) {
                        result = JSON.parse(data);
                        console.log(data);
                        if (result==true) {
                            $("#infoM").modal('show');
                        }else{
                            $("#noModifico").modal('show');
                        }                    
                    }
                });
            }
        </script>        
        <script type="text/javascript">
            $('#btnGuardado').click(function(){
                document.location.reload();
            });
        </script>
        <script type="text/javascript">
            $('#btnGuardado2').click(function(){
                document.location.reload();
            });
        </script>
        <script type="text/javascript">
            $('#btnModifico').click(function(){
                document.location = 'registrar_GF_COMPROBANTE_CONTABLE.php';
            });
        </script>
        <script type="text/javascript">
            $('#btnNoModifico').click(function(){
                document.location = 'registrar_GF_COMPROBANTE_CONTABLE.php';
            });
        </script>
        <script type="text/javascript">
            $('#ver1').click(function(){
                document.location = 'registrar_GF_COMPROBANTE_CONTABLE.php';
            });
        </script>
        <script type="text/javascript">    
            $('#ver2').click(function(){  
                document.location = 'registrar_GF_COMPROBANTE_CONTABLE.php';
            });
        </script>
        <script type="text/javascript" src="js/select2.js"></script>
    <div class="modal fade" id="modDesBal" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
                <div id="forma-modal" class="modal-header">		          
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                        <p>No puede abandonar este formulario ya que no está balanceado. Verifique nuevamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnDesBal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
        </div>
    </div>
</div>    
 <?php    
    if(!empty($_SESSION['idNumeroC']))
{ 
  $cierre = cierrecnt($_SESSION['idNumeroC']);
  if($cierre ==1){  ?>
  <script>
    $("#fecha").prop("disabled", true) ;
    $("#sltTipoC").prop("disabled", true) ;
    $("#txtNumero").prop("disabled", true) ;
    $("#sltTercero").prop("disabled", true) ;
    $("#sltCentroC").prop("disabled", true) ;
    $("#txtDescripcion").prop("disabled", true) ;
    $("#sltProyecto").prop("disabled", true) ;
    $("#sltClaseCT").prop("disabled", true);
    $("#txtNumeroCT").prop("disabled", true) ;
    $("#txtEstado").prop("disabled", true) ;
    $("#btnModificar").prop("disabled", true) ;
    $("#btnGuardar").prop("disabled", true) ;
    $("#sltcuenta").prop("disabled", true) ;   
    $("#slttercero").prop("disabled", true) ;   
    $("#sltcentroc").prop("disabled", true) ;   
    $("#sltproyecto").prop("disabled", true) ;   
    $("#txtValorD").prop("disabled", true) ;   
    $("#txtValorC").prop("disabled", true) ;   
    $("#btnDetalleComprobanteContable").prop("disabled", true) ;   
    $("#btnCopiar").prop("disabled", true) ;   
    
  </script>    
<?php } }   
  #####################################################
   #COPIAR COMPROBANTES ?>
  <script>   
          function copiar(){
              var tercero = $("#sltTercero").val();
              var id            =$("#id").val();
              var form_data ={tercero:tercero, id:id};
              if(id ==""){
                  
              } else {
              $("#modalCopiar").modal('show');
              }
              
          }
          
   </script> 
    <div class="modal fade" id="modalCopiar" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Copiar Comprobante</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                       <label class="col-sm-2 control-label"><strong class="obligado">*</strong>Comprobante:</label>
                                    
                        <?php 
                        ###TRAER TERCERO##
                        if(!empty($_SESSION['idNumeroC'])) { 
                                    $ter ="SELECT tercero, tipocomprobante FROM gf_comprobante_cnt WHERE id_unico = ".$_SESSION['idNumeroC'];
                                    $ter =$mysqli->query($ter);
                                    if(mysqli_num_rows($ter)>0){
                                        $ter = mysqli_fetch_row($ter);
                                        $terceroB = $ter[0];
                                        $tipoC = $ter[1];
                                    } else {
                                        $terceroB =0;
                                        $tipoC=0;
                                    }
                                    
                        } else { 
                            $terceroB =0;
                            $tipoC=0;
                        } 
                        # Consulta para datos de busqueda
                        $parametroAnno = $_SESSION['anno'];
                        ###########################################################################################################################
                        $sqlCP = "SELECT    cnt.id_unico,
                                            cnt.numero,
                                            tpc.sigla,
                                            DATE_FORMAT(cnt.fecha, '%d/%m/%Y') 
                                FROM        gf_comprobante_cnt cnt
                                LEFT JOIN   gf_tipo_comprobante tpc     ON cnt.tipocomprobante      = tpc.id_unico
                                LEFT JOIN   gf_tercero ter              ON cnt.tercero              = ter.id_unico
                                LEFT JOIN   gf_tipo_identificacion ti   ON ter.tipoidentificacion   = ti.id_unico
                                WHERE       tpc.clasecontable=15 AND cnt.parametrizacionanno = $parametroAnno 
                                AND cnt.tercero = $terceroB AND cnt.tipocomprobante = $tipoC AND cnt.id_unico != ".$_SESSION['idNumeroC']. " 
                                ORDER BY    cnt.numero DESC";
                        $resultCP = $mysqli->query($sqlCP);
                        ##########################################################################################################################
                        # Consulta para datos de busqueda                                        
                        ###########################################################################################################################
                        ?>
                        <select required="required" class="col-sm-4 input-sm" name="sltCop" id="sltCop" class="form-control" style="width:400px;" title="Seleccione Comprobante" required>
                        <?php                                        
                        echo "<option value=''>Comprobante</option>";###########################################################################################################################
                        while ($rowCP = mysqli_fetch_row($resultCP)) {
                            $f= $rowCP[3];
                            ######################################################################################################################
                            # Consulta de valor de comprobante
                            #
                            ######################################################################################################################
                           $sqlVA = "SELECT SUM(IF (dtc.valor<0, dtc.valor*-1, dtc.valor) )
                                                    FROM      gf_detalle_comprobante dtc 
                                                    LEFT JOIN gf_comprobante_cnt cnt ON dtc.comprobante = cnt.id_unico 
                                                    LEFT JOIN gf_cuenta c ON dtc.cuenta = c.id_unico 
                                                    WHERE     cnt.id_unico = $rowCP[0] AND (c.naturaleza = 1 
                                                    AND dtc.valor>0 OR c.naturaleza =2 AND dtc.valor<0);";
                            $resultVA = $mysqli->query($sqlVA);
                            $valorVA = mysqli_fetch_row($resultVA);
                            ######################################################################################################################
                            # Impresión de valores
                            ######################################################################################################################
                            echo "<option value=".$rowCP[0].">".$rowCP[1]." ".$rowCP[2]." ".$f." $".number_format($valorVA[0],2,',','.')."</option>";
                        }
                        ?>
                        </select>
                       <div><br/><br/><br/></div>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnCopiarC" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Copiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
   <script>
           $("#btnCopiarC").click(function(){
                var idcopiar = $("#sltCop").val();
                var idcnt = $("#id").val();
                var fecha = $("#fecha").val();
                if(idcopiar==""){
                    
                } else {
                    var form_data= {action:1, idcopiar:idcopiar, idcnt:idcnt, fecha:fecha};
                    $.ajax({
                        type: "POST",
                        url: "jsonPptal/gf_comprobante_cntJson.php",
                        data: form_data,
                        success: function(response)
                        {
                            console.log(response );
                          if(response ==1)  {
                              $("#modalCopiadoT").modal("show");
                          } else {
                              $("#modalCopiadoTNo").modal("show");
                          }
                        }
                      }); 
                }
               
           })
   </script>
   <div class="modal fade" id="modalCopiadoT" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Comprobante Copiado Correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="bntCopiadoT" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
        $('#bntCopiadoT').click(function()
        {
           document.location.reload();
           
        });
        </script>
        <div class="modal fade" id="modalCopiadoTNo" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Comprobante No Se Ha Copiado Correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="bntCopiadoTNo" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                        Aceptar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
        $('#bntCopiadoTNo').click(function()
        {
           document.location.reload();
           
        });
        </script>
     <script>
        $(".select2_single").select2({
            allowClear:true
        }); 
        $("#sltcuenta").select2({
            allowClear:true
        });
        $("#sltcentroc").select2({
            allowClear:true
        });
        $("#slttercero").select2({
            allowClear:true
        });
        $("#sltBuscar").select2({
            allowClear:true
        });
        $("#sltCop").select2({
            allowClear:true
        });
    </script>
    <div class="modal fade" id="mdlMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <labe style="font-height:normal" id="msj"></labe>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnMensaje1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    <button type="button" id="btnMensaje2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>  
    <script>
        $('#sltTercero').on('select2-open', function () {
            $('#s2id_autogen1_search').on("keydown", function(e) {
                let term = e.currentTarget.value;
                let form_data4 = {action: 8, term: term};

                $.ajax({
                    type:"POST",
                    url:"jsonPptal/gf_tercerosJson.php",
                    data:form_data4,
                    success: function(data){
                        let option = '<option value=""> - </option>';
                        console.log(data);
                         option = option+data;
                        $("#sltTercero").html(option);

                    }
                }); 
            });
        });
        $('#slttercero').on('select2-open', function () {
            $('#s2id_autogen6_search').on("keydown", function(e) {
                let term = e.currentTarget.value;
                let form_data4 = {action: 8, term: term};

                $.ajax({
                    type:"POST",
                    url:"jsonPptal/gf_tercerosJson.php",
                    data:form_data4,
                    success: function(data){
                        let option = '<option value=""> - </option>';
                        console.log(data);
                         option = option+data;
                        $("#slttercero").html(option);

                    }
                }); 
            });
        });

        
        function cargarT(id) {
            $("#sltTerceroV" + id).select2({placeholder: "Tercero", allowClear: true});
            $("#sltTerceroV" + id).on('select2-open', function () {
                $('.select2-input').keyup(function () {
                    var name = $(this).attr("id"); 
                    $('#'+name).on("keydown", function(e) {
                        let term = e.currentTarget.value;
                        let form_data4 = {action: 8, term: term};
                        console.log('tercero');
                        $.ajax({
                            type:"POST",
                            url:"jsonPptal/gf_tercerosJson.php",
                            data:form_data4,
                            success: function(data){
                                let option = '<option value=""> - </option>';
                                //console.log(data);
                                 option = option+data;
                                $("#sltTerceroV" + id).html(option);
                                    
                            }
                        }); 
                    }); 
                });
            });
        }
        
    </script>  
    <?php require_once './footer.php'; ?>
    <?php require_once './registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO.php'; ?>   
    </body>
    
</html>
 