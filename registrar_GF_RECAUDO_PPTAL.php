<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
#25/05/2017 | ERICA G. | AGREGO FORMATO
// Fecha de Creación  : 20/04/2017
// Hora de Creación   : 5:17 p.m
// Creado por         : Alexander Numpaque
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
require ('head_listar.php');
require ('Conexion/conexion.php');
require_once('./jsonSistema/funcionCierre.php');
$compania = $_SESSION['compania'];
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Inicializamos las variables
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$id_pptal = "";                                                     //Id de Comprobante
$recaudo = "";                                                      //Id de Comprobante con md5
$tipo = "";                                                         //Tipo de Comprobante
$numero = "";                                                       //Número de Comprobante
$fecha = "";                                                        //Fecha de Comprobante
$fechaV = "";                                                       //Fecha Vencimiento de Comprobante
$estado = "";                                                       //Estado del Comprobante
$nomEstado = "";                                                    //Nombre de estado del comprobante
$descripcion = "";                                                  //Descripción del Comprobante
$nomTercero = "";                                                   //Nombre del tercero
$nomTipo = "";                                                      //Nombre del tipo de comprobante
$tipoident = "";                                                    //Tipo identificacion del tercero
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Validmaos que la variable recaudo en la url no este vacia
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(!empty($_GET['recaudo'])) {
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //Consultamos los valores del comprobante
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $sqlR = "SELECT   ptal.id_unico,
                    date_format(ptal.fecha,'%d/%m/%Y'),
                    date_format(ptal.fechavencimiento,'%d/%m/%Y'),
                    ptal.tipocomprobante,
                    CONCAT(tpc.nombre,' ',tpc.codigo),
                    ptal.numero,
                    ptal.tercero,
                    IF( CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                IF(ter.apellidouno IS NULL,'',
                                  IF(ter.apellidouno IS NULL,'',ter.apellidouno)
                                ),' ',
                                IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' OR
                        CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                IF(ter.apellidouno IS NULL,'',
                                  IF(ter.apellidouno IS NULL,'',ter.apellidouno)
                                ),' ',
                                IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,
                                (ter.razonsocial),
                        CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                IF(ter.apellidouno IS NULL,'',
                                  IF(ter.apellidouno IS NULL,'',ter.apellidouno)
                                ),' ',
                                IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
                    CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD',
                    ptal.estado,
                    ptal.descripcion
          FROM      gf_comprobante_pptal ptal
          LEFT JOIN gf_tipo_comprobante_pptal tpc ON ptal.tipocomprobante = tpc.id_unico
          LEFT JOIN gf_tercero ter ON ter.id_unico = ptal.tercero
          LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
          WHERE     md5(ptal.id_unico) = '".$_GET['recaudo']."'";
  $resultR = $mysqli->query($sqlR);
  $rowR = mysqli_fetch_row($resultR);
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //Cargamos las variables
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $id_pptal = $rowR[0];
  $fecha = $rowR[1];
  $fechaV = $rowR[2];
  $tipo = $rowR[3];
  $nomTipo = $rowR[4];
  $numero = $rowR[5];
  $tercero = $rowR[6];
  $nomTercero = $rowR[7];
  $tipoident = $rowR[8];
  $estado = $rowR[9];
  $descripcion = $rowR[10];
  //
  $recaudo = $_GET['recaudo'];
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Validación para imprimir el estado dependiendo si esta vacio ó tiene algun valor
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(empty($estado)) {
  $sqlE = "SELECT nombre FROM gf_estado_comprobante_pptal WHERE id_unico = 1";    //Consulta para obtener el nombre del estado
  $resultE = $mysqli->query($sqlE);
  $rowE = mysqli_fetch_row($resultE);                                             //Obtenemos el valor retornado por la consulta
  $nomEstado = $rowE[0];                                                          //Asignamos el valor de nombre del estado en la variable $nomEstado
}else{
  $sqlE = "SELECT nombre FROM gf_estado_comprobante_pptal WHERE id_unico = $estado";  //Consulta para obtener el nombre del estado
  $resultE = $mysqli->query($sqlE);
  $rowE = mysqli_fetch_row($resultE);                                              //Obtenemos el valor retornado por la consulta
  $nomEstado = $rowE[0];                                                           //Asignamos el valor de nombre del estado en la variable $nomEstado
}
$anno = $_SESSION['anno'];
 ?>
    <title>Registrar Recaudo Presupuestal</title>
    <!-- Librerias de carga para el datapicker -->
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <!-- select2 -->
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <!-- Estilos -->
    <style>
      /*Estilo para cabeza*/
      .cabeza{font-size: 10px}
      /*Estilos para la tabla responsiva*/
      table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
      table.dataTable tbody td,table.dataTable tbody td{padding:1px}
      .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;font-family: Arial;}
      .input_det {height:24px;border-radius:3px;font-family:'Arial'}
    </style><!-- ./Estilos -->
  </head>
  <body >
    <div class="container-fluid"><!-- container-fluid -->
      <div class="row content"><!-- row content -->
        <?php require ('menu.php'); ?><!-- ./menu -->
        <div class="col-sm-10"><!-- col-sm-10 -->
          <h2 class="tituloform" align="center" style="margin-top:0px">Registrar Recaudo Presupuestal</h2><!-- ./Titulo del formulario -->
          <div class="client-form contenedorForma"><!-- client-form contenedorForma -->
            <form name="form" class="form-horizontal" method="POST" enctype="multipart/form-data" action="json/registrarGFRecaudoPptal_S.php?action=registrar" style="margin-bottom:-10px"><!-- Formulario -->
              <p align="center" class="parrafoO" style="margin-bottom:1em"><!-- Parrafo -->
                Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
              </p><!-- ./Parrafo -->
              <input type="hidden" name="id_pptal" id="id_pptal" value="<?php echo $id_pptal?>">
              <div class="form-group"><!-- form-group -->
                <label class="control-label col-sm-2"><!-- Label -->
                  <strong class="obligado">*</strong>Tipo de Comprobante:
                </label><!-- ./Label -->
                <select class="col-sm-1 form-control select2" name="sltTipoC" id="sltTipoC" title="Seleccione el tipo de comprobante" style="width:15%" required="" onchange="return new_value(this.value)"><!-- Select -->
                  <?php
                  if(!empty($tipo)){
                    echo "<option value=\"".$tipo."\">".$nomTipo."</option>";
                    $sqlTC = "SELECT id_unico,nombre,codigo FROM gf_tipo_comprobante_pptal "
                            . "WHERE clasepptal = 18 AND id_unico != $tipo AND compania = $compania ORDER BY codigo ASC"; //Consulta para obtener los tipos de comprobantes
                    $resultTC = $mysqli->query($sqlTC);
                    while ($rowTC = mysqli_fetch_row($resultTC)) {
                      echo "<option value=\"$rowTC[0]\">".$rowTC[1]." ".$rowTC[2]."</option>";
                    }
                  }else{
                    echo "<option value=\"\">Tipo de Comprobante</option>"; //Campo vacios
                    $sqlTC = "SELECT id_unico,nombre,codigo FROM gf_tipo_comprobante_pptal WHERE clasepptal = 18 AND compania = $compania ORDER BY codigo ASC"; //Consulta para obtener los tipos de comprobantes
                    $resultTC = $mysqli->query($sqlTC);
                    while ($rowTC = mysqli_fetch_row($resultTC)) {
                      echo "<option value=\"$rowTC[0]\">".$rowTC[1]." ".$rowTC[2]."</option>";
                    }
                  }
                   ?>
                </select><!-- ./Select -->
                <label class="col-sm-2 control-label"><!-- Label -->
                  <strong class="obligado">*</strong>Fecha:
                </label><!-- ./Label -->
                <input type="text" name="txtFecha" id="txtFecha" class="form-control col-sm-1" style="width:15%" title="Fecha" placeholder="Fecha" value="<?php echo $fecha; ?>" required="" readonly="true" ><!-- ./Input text-->
                
                <label class="col-sm-1 control-label"><!-- Label -->
                  <strong class="obligado">*</strong>Numero:
                </label><!-- ./Label -->
                <input type="text" name="txtNumero" id="txtNumero" class="form-control col-sm-1" style="width:15%" title="Número" placeholder="Número de Comprobante" required="" value="<?php echo $numero ?>" readonly=""/><!-- ./Input text -->
              </div><!-- ./form-group -->
              <div class="form-group" style="margin-top:-15px"><!-- form-group -->
                <label class="control-label col-sm-2"><!-- Label -->
                  <strong class="obligado">*</strong>Fecha de Vencimiento:
                </label><!-- ./Label -->
                <input type="text" name="txtFechaV" id="txtFechaV" class="form-control col-sm-1" style="width:15%" title="Fecha de Vencimiento" placeholder="Fecha de Vencimiento" value="<?php echo $fechaV; ?>" required=""><!-- ./Input text-->
                <label class="control-label col-sm-2"><!-- Label -->
                  <strong class="obligado">*</strong>Tercero:
                </label><!-- ./Label -->
                <select class="col-sm-2 form-control select2" name="sltTercero" id="sltTercero" title="Seleccione Tercero" style="width:38.4%" required=""><!-- Select -->
                  <?php
                  if(!empty($tercero)){
                    echo "<option value=\"$tercero\">".$nomTercero." ".$tipoident."</option>";
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    //Consulta para obtener el tercero
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $sqlT = "SELECT IF( CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                IF(ter.apellidouno IS NULL,'',
                                                  IF(ter.apellidouno IS NULL,'',ter.apellidouno)
                                                ),' ',
                                                IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' OR
                                        CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                IF(ter.apellidouno IS NULL,'',
                                                  IF(ter.apellidouno IS NULL,'',ter.apellidouno)
                                                ),' ',
                                                IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,
                                                (ter.razonsocial),
                                        CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                IF(ter.apellidouno IS NULL,'',
                                                  IF(ter.apellidouno IS NULL,'',ter.apellidouno)
                                                ),' ',
                                                IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
                                    ter.id_unico,
                                    CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD'
                            FROM gf_tercero ter
                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion
                            WHERE ter.id_unico != $tercero AND ter.compania =$compania";
                    $resultT = $mysqli->query($sqlT);
                    while ($rowT = mysqli_fetch_row($resultT)) {
                      echo "<option value=\"".$rowT[1]."\">".ucwords(mb_strtolower($rowT[0].' '.$rowT[2]))."</option>";
                    }
                  }else{
                    echo "<option value=\"\">Tercero</option>"; //Campo vacio
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    //Consulta para obtener el tercero
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $sqlT = "SELECT IF( CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                IF(ter.apellidouno IS NULL,'',
                                                  IF(ter.apellidouno IS NULL,'',ter.apellidouno)
                                                ),' ',
                                                IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' OR
                                        CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                IF(ter.apellidouno IS NULL,'',
                                                  IF(ter.apellidouno IS NULL,'',ter.apellidouno)
                                                ),' ',
                                                IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,
                                                (ter.razonsocial),
                                        CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                IF(ter.apellidouno IS NULL,'',
                                                  IF(ter.apellidouno IS NULL,'',ter.apellidouno)
                                                ),' ',
                                                IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
                                    ter.id_unico,
                                    CONCAT(ti.nombre,' - ',ter.numeroidentificacion) AS 'TipoD'
                            FROM gf_tercero ter
                            LEFT JOIN gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion 
                            WHERE ter.compania = $compania";
                    $resultT = $mysqli->query($sqlT);
                    while ($rowT = mysqli_fetch_row($resultT)) {
                      echo "<option value=\"".$rowT[1]."\">".ucwords(mb_strtolower($rowT[0].' '.$rowT[2]))."</option>";
                    }
                  }
                   ?>
                </select><!-- ./Select -->
              </div><!-- ./form-group -->
              <div class="form-group" style="margin-top:-15px"><!-- form-group -->
                <label class="col-sm-2 control-label"><!-- Label -->
                  <strong class="obligado">*</strong>Estado:
                </label><!-- ./Label -->
                <input type="text" name="txtEstado" id="txtEstado" class="form-control col-sm-1" style="width:15%" title="Estado del Comprobante" placeholder="Estado" value="<?php echo $nomEstado; ?>" readonly=""><!-- ./Input text -->
                <label class="col-sm-2 control-label"><!-- Label -->
                  Descripción:
                </label><!-- ./Label -->
                <textarea name="txtDescripcion" id="txtDescripcion" class="form-control col-sm-1" style="width:38.4%;margin-top:0px;height:34px" placeholder="Descripción" onkeyup="txtValida(event,'num_car')"><?php echo $descripcion; ?></textarea><!-- ./Textarea -->
              </div><!-- ./form-group -->
              <div class="form-group" style="margin-top:-15px"><!-- form-group -->
                <label class="control-label col-sm-2"><!-- label -->
                  </strong>Buscar Comprobante:
                </label><!--./label -->
                <select class="col-sm-2 form-control select2" name="sltBuscar" id="sltBuscar" title="Buscar Comprobante" style="width:20%" onchange="see_loop(this.value)"><!-- Select -->
                  <?php
                  echo "<option value=\"\">Buscar Comprobante</option>";
                  $sqlB = "SELECT     ptal.id_unico,
                                      ptal.numero,
                                      tpc.codigo,
                                      date_format(ptal.fecha,'%d/%m/%Y'),
                                      IF(CONCAT_WS(' ',
                                        tr.nombreuno,
                                        tr.nombredos,
                                        tr.apellidouno,
                                        tr.apellidodos) 
                                        IS NULL OR CONCAT_WS(' ',
                                        tr.nombreuno,
                                        tr.nombredos,
                                        tr.apellidouno,
                                        tr.apellidodos) = '',
                                        (tr.razonsocial),
                                        CONCAT_WS(' ',
                                        tr.nombreuno,
                                        tr.nombredos,
                                        tr.apellidouno,
                                        tr.apellidodos)) AS NOMBRE,

                                      date_format(ptal.fecha,'%d/%m/%Y')
                          
                          FROM   gf_comprobante_pptal ptal 
                          LEFT JOIN   gf_tipo_comprobante_pptal tpc ON tpc.id_unico = ptal.tipocomprobante
                          LEFT JOIN   gf_tercero tr            ON tr.id_unico = ptal.tercero
                          WHERE       tpc.clasepptal = 18 
                          AND ptal.parametrizacionanno = $anno 
                          ORDER BY ptal.numero DESC";
                          $resultB = $mysqli->query($sqlB);
                          while ($rowB = mysqli_fetch_row($resultB)) {
                            $sqlValor = 'SELECT SUM(valor)
                                   FROM gf_detalle_comprobante_pptal
                                   WHERE comprobantepptal = '.$rowB[0];
                            $valor  = $mysqli->query($sqlValor);
                            $rowV   = mysqli_fetch_row($valor);
                            $value  =' $'.number_format($rowV[0], 2, '.', ',');
                            echo "<option value=\"$rowB[0]\">".$rowB[1]." ".mb_strtoupper($rowB[2])." ".($rowB[3])." ".ucwords(mb_strtolower($rowB[4])).$value."</option>";
                          }
                   ?>
                </select><!--./select -->
                <div class="col-sm-3 col-sm-offset-4"><!-- col-sm-7 -->
                  <a id="btnNuevo" name="btnNuevo" title="Nuevo" class="btn btn-primary" style="box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;" onclick="return_url()"><!-- link -->
                    <li class="glyphicon glyphicon-plus"></li><!-- ./li -->
                  </a><!-- ./link -->
                  <button type="submit" id="btnGuardar" name="btnGuardar" title="Guardar" class="btn btn-primary" style="box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;"><!-- link -->
                    <li class="glyphicon glyphicon-floppy-disk"></li><!-- ./li -->
                  </button><!-- ./link -->
                  <a id="btnImprimir" name="btnImprimir" title="Imprimir" class="btn btn-primary" style="box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;"><!-- link -->
                    <li class="glyphicon glyphicon-print"></li><!-- ./li -->
                  </a><!-- ./link -->
                  <!--Funcion Imprimir-->
                  <script>
                      $("#btnImprimir").click(function(){
                          window.open ("informesPptal/inf_Exp_Recaudo_Pptal.php?id=<?php echo md5($id_pptal)?>")
                      });
                    </script> 
                  <a id="btnEditar" name="btnEditar" title="Modificar" class="btn btn-primary" style="box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;" onclick="update_head($('#sltTercero').val(),$('#txtDescripcion').val(),<?php echo $id_pptal ?>)"><!-- link -->
                    <li class="glyphicon glyphicon-edit"></li><!-- ./li -->
                  </a><!-- ./link -->
                </div><!-- ./col-sm-3 -->
              </div><!-- ./form-group -->
            </form><!-- ./Formulario -->
          </div><!-- ./client-form contenedorForma -->
        </div><!-- ./col-sm-10 -->
        <div class="col-sm-10" ><!-- col-sm-10 -->
          <div class="col-sm-12" style="margin-top:5px;border-radius: 5px;box-shadow: inset 1px 1px 1px 1px gray;"><!-- col-sm-12 -->
            <form name="form_detalle" class="form-horizontal" method="post" enctype="multipart/form-data" action="registrarDetalleRecaudoPptal_S.php?action=registrar" style="margin-bottom:-7px"><!-- form -->
              <div class="form-group" style="margin-top:8px"><!-- form-group -->
                <label class="col-sm-1 control-label"><!-- Label -->
                  <strong class="obligado">*</strong>Concepto
                </label><!-- ./label -->
                <select class="col-sm-1 form-control select2" name="sltConcepto" id="sltConcepto" title="Seleccione Concepto" style="width:15%;" required="" onchange="get_rubro(this.value)"><!-- Select -->
                  <?php
                  echo "<option value=\"\">Concepto</option>";//Campo vacio
                  $sqlC = "SELECT id_unico,nombre FROM gf_concepto where parametrizacionanno = $anno ORDER BY nombre ASC";//Consulta obtener todos los conceptos
                  $resultC = $mysqli->query($sqlC);
                  while ($rowC = mysqli_fetch_row($resultC)) {
                    echo "<option value=\"".$rowC[0]."\">".ucwords(mb_strtolower($rowC[1]))."</option>";//Imprimos los conceptos
                  }
                   ?>
                </select><!-- ./Select -->
                <label class="col-sm-1 control-label"><!-- Label -->
                  <strong class="obligado">*</strong>Rubro:
                </label><!-- ./label -->
                <select class="col-sm-1 form-control" name="sltRubro" id="sltRubro" title="Seleccione Rubro" style="width:15%;font-size:10px" required="" onchange="get_fuente(this.value)"><!-- Select -->
                  <?php echo "<option value=\"\">Rubro</option>"; ?>
                </select><!-- ./Select -->
                <label class="col-sm-1 control-label"><!-- Label -->
                  <strong class="obligado">*</strong>Fuente:
                </label><!-- ./label -->
                <select class="col-sm-1 form-control select2" name="sltFuente" id="sltFuente" title="Seleccione Fuente" style="width:15%" required=""><!-- Select -->
                  <?php echo "<option value=\"\">Fuente</option>"; //Campo vacio ?>
                </select><!-- ./Select -->
                <label class="col-sm-1 control-label"><!-- Label -->
                  <strong class="obligado">*</strong>Valor:
                </label><!-- ./label -->
                <input type="text" class="col-sm-1 form-control" name="txtValor" id="txtValor" title="Ingrese el valor" style="width:12%;font-size:10px" placeholder="Valor" onkeyup="txtValida(event,'num')" required=""><!-- ./Input -->
                <div class="col-sm-1">
                  <a class="btn btn-primary" id="btnGuardarD" style="box-shadow: 1px 1px 1px 1px gray;color:#fff;border-color:#1075C1;" onclick="guardarDetalle($('#sltConcepto').val(),$('#sltRubro').val(),$('#sltFuente').val(),$('#txtValor').val(),<?php echo $id_pptal ?>,$('#sltTercero').val())"><li class="glyphicon glyphicon-floppy-disk"></li></a>
                </div>
                <!-- Scripts de validación para activar o desactivar los botones -->
               
              </div><!--  ./form-group -->
            </form><!-- ./form -->
          </div><!-- ./col-sm-12 -->
        </div><!-- ./col-sm-10 -->
        <input type="hidden" id="idPrevio" value=""><!-- ./Campo oculto -->
        <input type="hidden" id="idActual" value=""><!-- ./Campo oculto -->
        <div class="col-sm-10"><!-- ./col-sm-10 -->
          <div class="table-responsive contTabla"><!-- table responsive contTabla -->
            <table id="tabla" class="table table-striped table-condensed display detalle" cellpadding="0" width="100%">
              <thead><!-- thead -->
                <tr><!-- tr -->
                  <td class="oculto"></td><!-- ./td -->
                  <td class="cabeza" width="7%"></td><!-- ./td -->
                  <td class="cabeza">Concepto</td><!-- ./td -->
                  <td class="cabeza">Rubro</td><!-- ./td -->
                  <td class="cabeza">Fuente</td><!-- ./td -->
                  <td class="cabeza">Valor</td><!-- ./td -->
                </tr><!-- ./tr -->
                <tr><!-- tr -->
                  <th class="oculto"></th><!-- ./th -->
                  <th class="cabeza" width="7%"></th><!-- ./th -->
                  <th class="cabeza">Concepto</th><!-- ./th -->
                  <th class="cabeza">Rubro</th><!-- ./th -->
                  <th class="cabeza">Fuente</th><!-- ./th -->
                  <th class="cabeza">Valor</th><!-- ./th -->
                </tr><!-- ./tr -->
              </thead><!-- ./thead -->
              <tbody><!-- tbody -->
                <?php
                //Validamos que el di del comprobante no este vacia
                if(!empty($id_pptal)){
                  //Consult apara obtener los valores del detalle
                  $sql = "SELECT  dtp.id_unico,
                  cn.nombre,
                  rb.codi_presupuesto,
                  rb.nombre,
                  fte.nombre,
                  dtp.valor
                  FROM      gf_detalle_comprobante_pptal dtp
                  LEFT JOIN gf_concepto_rubro crb ON crb.id_unico = dtp.conceptorubro
                  LEFT JOIN gf_concepto cn        ON cn.id_unico  = crb.concepto
                  LEFT JOIN gf_rubro_pptal rb     ON rb.id_unico  = crb.rubro
                  LEFT JOIN gf_rubro_fuente rbf   ON rbf.id_unico = dtp.rubrofuente
                  LEFT JOIN gf_fuente fte         ON fte.id_unico = rbf.fuente
                  WHERE dtp.comprobantepptal =  $id_pptal";
                  $result = $mysqli->query($sql);
                  //Imprimimos los valores
                  while ($row = mysqli_fetch_row($result)) {
                    echo "<tr>\n";
                    echo "<td class=\"oculto\"></td>\n";
                    echo "<td width=\"7%\">\n";
                    $cierre = cierre($id_pptal);
                    if ($cierre == 0) { 
                    echo "<a href=\"#".$row[0]."\" onclick=\"delete_detalle(".$row[0].")\"><li class=\"glyphicon glyphicon-trash\"></li></a>";
                    echo "<a href=\"#".$row[0]."\" onclick=\"show_block(".$row[0].")\"><li class=\"glyphicon glyphicon-edit\"></li></a>";
                    }
                    echo "</td>\n";
                    echo "<td>".ucwords(mb_strtolower($row[1]))."</td>\n";
                    echo "<td>".ucwords(mb_strtolower($row[2]." ".$row[3]))."</td>\n";
                    echo "<td>".ucwords(mb_strtolower($row[4]))."</td>\n";
                    echo "<td class=\"text-right\">\n";
                    echo "<label style=\"font-weight:normal;font-size:10px\" id=\"lblValor$row[0]\">".number_format($row[5],2,',','.')."</label>\n";
                    echo "<input type=\"text\" class=\"col-sm-9 input_det\" id=\"txtValor$row[0]\" value=\"$row[5]\" style=\"display:none;\"/>\n";
                    echo "<div class=\"col-sm-1\">\n";
                    echo "<table id=\"tab$row[0]\" style=\"padding:0px;background-color:transparent;background:transparent\">\n";
                    echo "<tr style=\"background-color:transparent;\">\n";
                    echo "<td style=\"background-color:transparent;\">\n";
                    echo "<a  href=\"#$row[0]\" title=\"Guardar\" id=\"guardar$row[0]\" style=\"display: none;\" onclick=\"save_changes($row[0])\">\n";
                    echo "<li class=\"glyphicon glyphicon-floppy-disk\"></li>\n";
                    echo "</a>\n";
                    echo "</td>\n";
                    echo "<td style=\"background-color:transparent;\">\n";
                    echo "<a href=\"#$row[0]\" title=\"Cancelar\" id=\"cancelar$row[0]\" style=\"display: none;\" onclick=\"cancel_update($row[0])\" >\n";
                    echo "<i title=\"Cancelar\" class=\"glyphicon glyphicon-remove\"></i>\n";
                    echo "</a>\n";
                    echo "</td>\n";
                    echo "</tr>\n";
                    echo "</table>\n";
                    echo "</div>\n";
                    echo "</td>\n";
                    echo "</tr>\n";
                  }
                }
                ?>
              </tbody><!-- ./tbody -->
            </table><!-- ./table -->
          </div><!-- ./table ./responsive contTabla -->
        </div>
      </div><!-- ./row content-->
    </div><!-- ./container-fluid -->
    <div><?php require ('footer.php'); ?></div><!-- ./footer -->
    <div class="modal fade" id="myModal1" role="dialog" align="center" ><!-- modal -->
      <div class="modal-dialog"><!-- modal-dialog -->
        <div class="modal-content"><!-- modal-content -->
          <div id="forma-modal" class="modal-header"><!-- modal-header -->
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4><!-- ./p -->
          </div><!-- ./modal-header -->
          <div class="modal-body" style="margin-top: 8px"><!-- modal-body -->
            <p>Información guardada correctamente.</p><!-- ./p -->
          </div><!-- ./modal-body -->
          <div id="forma-modal" class="modal-footer"><!-- modal-footer -->
            <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" onclick="reload_page()">Aceptar</button><!-- ./button -->
          </div><!-- ./modal-footer -->
        </div><!-- ./modal-content -->
      </div><!-- ./modal-dialog -->
    </div><!-- ./modal -->
    <div class="modal fade" id="myModal2" role="dialog" align="center" ><!-- modal -->
      <div class="modal-dialog"><!-- modal-dialog -->
        <div class="modal-content"><!-- ./modal-content -->
          <div id="forma-modal" class="modal-header"><!-- modal-header -->
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4><!-- ./p -->
          </div><!-- ./modal-header -->
          <div class="modal-body" style="margin-top: 8px"><!-- modal-body -->
            <p>No se ha podido guardar la información.</p><!-- ./p -->
          </div><!-- ./modal-body -->
          <div id="forma-modal" class="modal-footer"><!-- modal-footer -->
            <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button><!-- ./button -->
          </div><!-- ./modal-footer -->
        </div><!-- ./modal-content -->
      </div><!-- ./modal-dialog -->
    </div><!-- ./modal -->
    <div class="modal fade" id="modalInicioE" role="dialog" align="center" ><!-- modal -->
      <div class="modal-dialog"><!-- modal-dialog -->
        <div class="modal-content"><!-- modal-content -->
          <div id="forma-modal" class="modal-header"><!-- modal-header -->
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4><!-- ./p -->
          </div><!-- ./modal-header -->
          <div class="modal-body" style="margin-top: 8px"><!-- modal-body -->
            <p>¿Desea eliminar el registro seleccionado de Recaudo?</p>
          </div><!-- ./modal-body -->
          <div id="forma-modal" class="modal-footer"><!-- modal-footer -->
            <button type="button" id="btnInicioE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" onclick="reload_page()">Aceptar</button><!-- ./button -->
          </div><!-- ./modal-footer -->
        </div><!-- ./modal-content -->
      </div><!-- ./modal-dialog -->
    </div><!-- ./modal -->
    <div class="modal fade" id="modalEliminado" role="dialog" align="center" ><!-- modal -->
      <div class="modal-dialog"><!-- modal-dialog -->
        <div class="modal-content"><!-- ./modal-content -->
          <div id="forma-modal" class="modal-header"><!-- modal-header -->
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4><!-- ./p -->
          </div><!-- ./modal-header -->
          <div class="modal-body" style="margin-top: 8px"><!-- modal-body -->
            <p>Información eliminada correctamente.</p>
          </div><!-- ./modal-body -->
          <div id="forma-modal" class="modal-footer"><!-- modal-footer -->
            <button type="button" id="btnEliminado" onclick="reload_page()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button><!-- ./button -->
          </div><!-- ./modal-footer -->
        </div><!-- ./modal-content -->
      </div><!-- ./modal-dialog -->
    </div><!-- ./modal -->
    <div class="modal fade" id="modalNoEliminado" role="dialog" align="center" ><!-- modal -->
      <div class="modal-dialog"><!-- modal-dialog -->
        <div class="modal-content"><!-- ./modal-content -->
          <div id="forma-modal" class="modal-header"><!-- modal-header -->
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4><!-- ./p -->
          </div><!-- ./modal-header -->
          <div class="modal-body" style="margin-top: 8px"><!-- modal-body -->
            <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
          </div><!-- ./modal-body -->
          <div id="forma-modal" class="modal-footer"><!-- modal-footer -->
            <button type="button" id="btnNoEliminado" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button><!-- ./button -->
          </div><!-- ./modal-footer -->
        </div><!-- ./modal-content -->
      </div><!-- ./modal-dialog -->
    </div><!-- ./modal -->
    <div class="modal fade" id="modalModificado" role="dialog" align="center" ><!-- modal -->
      <div class="modal-dialog"><!-- modal-dialog -->
        <div class="modal-content"><!-- ./modal-content -->
          <div id="forma-modal" class="modal-header"><!-- modal-header -->
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4><!-- ./p -->
          </div><!-- ./modal-header -->
          <div class="modal-body" style="margin-top: 8px"><!-- modal-body -->
            <p>Información modificada correctamente.</p>
          </div><!-- ./modal-body -->
          <div id="forma-modal" class="modal-footer"><!-- modal-footer -->
            <button type="button" id="btnModificado" onclick="reload_page()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button><!-- ./button -->
          </div><!-- ./modal-footer -->
        </div><!-- ./modal-content -->
      </div><!-- ./modal-dialog -->
    </div><!-- ./modal -->
    <div class="modal fade" id="modalNoModificado" role="dialog" align="center" ><!-- modal -->
      <div class="modal-dialog"><!-- modal-dialog -->
        <div class="modal-content"><!-- ./modal-content -->
          <div id="forma-modal" class="modal-header"><!-- modal-header -->
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4><!-- ./p -->
          </div><!-- ./modal-header -->
          <div class="modal-body" style="margin-top: 8px"><!-- modal-body -->
            <p>No se ha podido modificar la información.</p>
          </div><!-- ./modal-body -->
          <div id="forma-modal" class="modal-footer"><!-- modal-footer -->
            <button type="button" id="btnModificado" onclick="reload_page()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button><!-- ./button -->
          </div><!-- ./modal-footer -->
        </div><!-- ./modal-content -->
      </div><!-- ./modal-dialog -->
    </div><!-- ./modal -->
    <div class="modal fade" id="modalNoModificado" role="dialog" align="center" ><!-- modal -->
      <div class="modal-dialog"><!-- modal-dialog -->
        <div class="modal-content"><!-- ./modal-content -->
          <div id="forma-modal" class="modal-header"><!-- modal-header -->
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4><!-- ./p -->
          </div><!-- ./modal-header -->
          <div class="modal-body" style="margin-top: 8px"><!-- modal-body -->
            <p>No se ha podido modificar la información.</p>
          </div><!-- ./modal-body -->
          <div id="forma-modal" class="modal-footer"><!-- modal-footer -->
            <button type="button" id="btnModificado" onclick="reload_page()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button><!-- ./button -->
          </div><!-- ./modal-footer -->
        </div><!-- ./modal-content -->
      </div><!-- ./modal-dialog -->
    </div><!-- ./modal -->
    <div class="modal fade" id="mdlMsj" role="dialog" align="center" ><!-- modal -->
      <div class="modal-dialog"><!-- modal-dialog -->
        <div class="modal-content"><!-- ./modal-content -->
          <div id="forma-modal" class="modal-header"><!-- modal-header -->
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4><!-- ./p -->
          </div><!-- ./modal-header -->
          <div class="modal-body" style="margin-top: 8px"><!-- modal-body -->
              <label id="lblMensaje"></label>
          </div><!-- ./modal-body -->
          <div id="forma-modal" class="modal-footer"><!-- modal-footer -->
            <button type="button" id="btnMsj"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button><!-- ./button -->
          </div><!-- ./modal-footer -->
        </div><!-- ./modal-content -->
      </div><!-- ./modal-dialog -->
    </div><!-- ./modal -->
    <script type="text/javascript" src="js/select2.js"></script><!-- ./Script para la libreria select js -->
    
    <script>
    $("#txtFecha").change(function(){
        var tipComPal = $("#sltTipoC").val();
        if(tipComPal==""){
            $("#lblMensaje").html('Escoja Comprobante');
            $("#mdlMsj").modal('show');
            $("#txtFechaV").val("").focus();
            $("#txtFecha").val("").focus();
        } else {
        var fecha = $("#txtFecha").val();
        var form_data = { case: 4, fecha:fecha};
        $.ajax({
            type: "POST",
            url: "jsonSistema/consultas.php",
            data: form_data,
            success: function(response)
            { 
                console.log('acc'+response);
                if(response ==1){
                    $("#lblMensaje").html('Periodo Cerrado.');
                    $("#mdlMsj").modal('show');
                    $("#txtFechaV").val("").focus();
                    $("#txtFecha").val("").focus();
                } else {
                    fecha1();
                }
            }
        });   
        }
    });
</script>
<script>
    function fecha1(){
        var tipComPal   = $("#sltTipoC").val();
        var fecha       = $("#txtFecha").val();
        var num         = $("#txtNumero").val();
        <?php if(!empty($_GET['recaudo'])) {?>
        var form_data = { estruc: 20, tipComPal: tipComPal, fecha: fecha, num:num };
        <?php } else { ?>
        var idComPptal = $("#id_pptal").val();
        var form_data = { estruc: 22, tipComPal: tipComPal, fecha: fecha, num:num, idComPptal:idComPptal };
        <?php } ?>
        $.ajax({
        type: "POST",
        url: "jsonPptal/validarFechas.php",
        data: form_data,
        success: function(response)
        { 
          console.log(response);
          if(response == 1)
          {
              $("#txtFechaV").val("").focus();
              $("#txtFecha").val("").focus();
              $("#lblMensaje").html('Fecha Incorrecta');
              $("#mdlMsj").modal('show');
              
          }
          else
          { 
            response = response.replace(' ',"");
            response= $.trim( response );
            $("#txtFechaV").val(response);
          }
        }
      }); 
    }
</script> 
                
    <script type="text/javascript">
    //Función para ejeuctar datapicker
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
        var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
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
          yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#txtFecha").datepicker({changeMonth: true}).val();
        $("#txtFechaV").datepicker({changeMonth: true}).val();
      });
      //Función de suma de dias entre fechas, recibe dias y la fecha a sumar
      sumaFecha = function(d, fecha){
        var Fecha = new Date();             //Creamos variable de fecha
        var sFecha = fecha || (Fecha.getDate() + "/" + (Fecha.getMonth() +1) + "/" + Fecha.getFullYear());
        var sep = sFecha.indexOf('/') != -1 ? '/' : '-';
        var aFecha = sFecha.split(sep);
        var fecha = aFecha[2]+'/'+aFecha[1]+'/'+aFecha[0];
        fecha= new Date(fecha);
        fecha.setDate(fecha.getDate()+parseInt(d));
        var anno=fecha.getFullYear();
        var mes= fecha.getMonth()+1;
        var dia= fecha.getDate();
        mes = (mes < 10) ? ("0" + mes) : mes;
        dia = (dia < 10) ? ("0" + dia) : dia;
        var fechaFinal = dia+sep+mes+sep+anno;
        return (fechaFinal);
      }
      //Llamado de la libreria select2
      $(".select2").select2({
        allowClear:true
      });
      $("#sltRubro").select2({
        allowClear:true,
        placeholder:'Rubro'
      });
      //Función para generar consecutivo nuevo
      function new_value(tipo){
        //Capturamos el valor enviado
        var form_data= {
          estruc: 3, id_tip_comp:tipo
        };
        //Envio ajax
        $.ajax({
          type:'POST',
          url:'estructura_expedir_disponibilidad.php',
          data:form_data,
          success: function(data){
              data = data.replace(' ',"");
              data= $.trim( data );
            $("#txtNumero").val(data); //Enviamos el valor devuelto al numero
          }
        });
      }
      //Funcion para sumar 30 dias para la fecha de Vencimiento
      function date_sum(fecha) {
        var fechaV = sumaFecha(30,fecha); //Invocamos la función fecha enviamos la fecha seleccionada en el campo
        $("#txtFechaV").val(fechaV);      //Asignamos la fecha al campo fecha de Vencimiento
      }
      //Función para retornar a la url limpia y sin variables
      function return_url(){
        window.location = 'registrar_GF_RECAUDO_PPTAL.php'; //Redireccionamos la url
      }
      //Función para recargar la página
      function reload_page(){
        window.location.reload();   //Recargamos la pagina
      }
      //Función para obtener los rubos relacionados al rubro
      function get_rubro(concepto){
        //Variable de envio
        var form_data = {
          concepto:concepto,
          existente:54
        };
        //Envio ajax
        $.ajax({
          type:'POST',
          url:'consultasBasicas/consultarNumeros.php',
          data:form_data,
          success: function(data,textStatus,jqXHR) {
            $("#sltRubro").html(data).fadeIn();
            $("#sltRubro").css('display','none');
          }
        }).error(function(data,textError,jqXHR){
          alert('data :'+data+', Error :'+textError+', jqXHR :'+jqXHR);
        });
      }
      //Función para obtener las fuentes
      function get_fuente(rubro){
        //Variable de envio
        var form_data = {
          rubro:rubro,
          existente:55
        };
        //Envio ajax
        $.ajax({
          type:'POST',
          url:'consultasBasicas/consultarNumeros.php',
          data:form_data,
          success: function(data,textStatus,jqXHR) {
            $("#sltFuente").html(data).fadeIn();
            $("#sltFuente").css('display','none');
          }
        }).error(function(data,textError,jqXHR){
          alert('data :'+data+', Error :'+textError+', jqXHR :'+jqXHR);
        });
      }
      //Función para gudardar detalle
      function guardarDetalle(concepto,rubro,fuente,valor,comprobante,tercero) {
        //Variable de envio
        var form_data = {
          concepto:concepto,
          rubro:rubro,
          fuente:fuente,
          valor:valor,
          comprobante:comprobante,
          tercero:tercero,
          action:'registrar'
        };
        var result = '';
        //Envio ajax
        $.ajax({
          type:'POST',
          url:'json/registrarDetalleRecaudoPptal_S.php',
          data:form_data,
          success: function(data,textStatus,jqXHR){
            result = JSON.parse(data);
            if(result == true){
              $("#myModal1").modal('show');
            }else{
              $("#myModal2").modal('show');
            }
          }
        }).error(function(data,textError,jqXHR){
          alert('data :'+data+' , textError'+textError+', jqXHR: '+jqXHR);
        });
      }
      //Función para eliminar el detalle
      function delete_detalle(id_detalle) {
        //Array de envio
        var form_data = {
          id:id_detalle,
          action:'eliminar'
        };
        //Variable de envio de resultado
        var result = "";
        //Envio ajax
        $.ajax({
          type:'POST',
          url:'json/registrarDetalleRecaudoPptal_S.php',
          data:form_data,
          success: function(data,textStatus,jqXHR) {
            result = JSON.parse(data);
            if(result == true) {
              $("#modalEliminado").modal('show');
            }else{
              $("#modalNoEliminado").modal('show');
            }
          }
        });
      }
      //Función para mostrar el campo para modificar el valor y la división para cancelar
      function show_block(id){
        if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
          //Labels
          var lblValorP = 'lblValor'+$("#idPrevio").val();
          //Campos oculto
          var txtValorP = 'txtValor'+$("#idPrevio").val();
          //Tabla oculta
          var tablaP = 'tab'+$("#idPrevio").val();
          //Botones ocultos
          var guardarP = 'guardar'+$("#idPrevio").val();
          var cancelarP = 'cancelar'+$("#idPrevio").val();
          //Ocultamos los campos
          $("#"+txtValorP).css('display','none');
          //Mostramos los Labels
          $("#"+lblValorP).css('display','block');
          //Ocultamos la tabla
          $("#"+tablaP).css('display','none');
          //Ocultamos los botones
          $("#"+guardarP).css('display','none');
          $("#"+cancelarP).css('display','none');
        }
        //Labels
        var lblValor = 'lblValor'+id;
        //Campo oculto
        var txtValor = 'txtValor'+id;
        //Tabla oculta
        var tabla = 'tab'+id;
        //Botones ocultos
        var guardar = 'guardar'+id;
        var cancelar = 'cancelar'+id;
        //mostramos los campos
        $("#"+txtValor).css('display','block');
        //ocultamos los Labels
        $("#"+lblValor).css('display','none');
        //Mostramos la tabla
        $("#"+tabla).css('display','block');
        //Mostramos los botones
        $("#"+guardar).css('display','block');
        $("#"+cancelar).css('display','block');
        //Cargamos el campo oculto con el di
        $("#idActual").val(id);
        //Validamos si el campo id previo esta vacio lo cargamos
        if($("#idPrevio").val() != id){
          $("#idPrevio").val(id);
        }
      }
      //Función para cancelar las actualizaciones
      function cancel_update(id) {
        //Labels
        var lblValor = 'lblValor'+id;
        //Campo oculto
        var txtValor = 'txtValor'+id;
        //Tabla oculta
        var tabla = 'tab'+id;
        //Botones ocultos
        var guardar = 'guardar'+id;
        var cancelar = 'cancelar'+id;
        //Ocultamos el campo
        $("#"+txtValor).css('display','none');
        //Mostramos el Label
        $("#"+lblValor).css('display','block');
        //Ocultamos la tabla
        $("#"+tabla).css('display','none');
        //Ocultamos los botones
        $("#"+guardar).css('display','none');
        $("#"+cancelar).css('display','none');
      }
      //Función para guardar cambioes en ele detalle
      function save_changes(id) {
        //Capturamos valor
        var valor = $("#txtValor"+id).val();
        //Array de envio
        var form_data = {
          id:id,
          txtValor:valor,
          action:'actualizar'
        };
        //Variable de captura del valor retornado
        var result = '';
        //Envio ajax
        $.ajax({
          type:'POST',
          url:'json/registrarDetalleRecaudoPptal_S.php',
          data:form_data,
          success:function(data,textStatus,jqXHR) {
            result = JSON.parse(data); //Convertimos el valor enviado
            if(result == true){
              $("#modalModificado").modal('show');
            }else{
              $("#modalNoModificado").modal('show');
            }
          }
        }).error(function(data,textError,jqXHR) {
          alert('data :'+data+', textError :'+textError+', jqXHR :'+jqXHR);
        });
      }
      //Función para modificar el comprobante
      function update_head(tercero,descripcion,id) {
        var id = parseInt(id);
        if(!isNaN(id)){
          //Variable y array de envio
          var form_data = {
            id:id,
            tercero:tercero,
            descripcion:descripcion,
            action:'update_head'
          };
          //Variable de captura de respuesta
          var result = '';
          //Envio ajax
          $.ajax({
            type:'POST',
            url:'json/registrarDetalleRecaudoPptal_S.php',
            data:form_data,
            success: function(data,textStatus,jqXHR){
              //Capturamos el valor retornado
              result = JSON.parse(data);
              //Validación de respuesta
              if(result == true){
                $("#modalModificado").modal('show');
              }else{
                $("#modalNoModificado").modal('show');
              }
            }
          }).error(function(data,textError,jqXHR) {
            alert('data :'+data+', textError :'+textError+', jqXHR :'+jqXHR);
          });
        }
      }
      //Función para limipiar los campos cuando regrese el formulario
      function clean_inputs(){
        $("#sltConcepto").prop('selectedIndex',0);
        $("#sltRubro").prop('selectedIndex',0);
        $("#sltFuente").prop('selectedIndex',0);
      }
      //Función para buscar los comprobantes
      function see_loop(id_p){
        //Variable del valor seleccionado del tercero
        var id_p = parseInt(id_p);
        //Validamos que no este vacio
        if(!isNaN(id_p)){
          //Array de envio
          var form_data = {
            existente:56,
            id_p:id_p
          };
          //Envio ajax
          $.ajax({
            type:'POST',
            url:'consultasBasicas/consultarNumeros.php',
            data:form_data,
            success: function(data,textStatus,jqXHR){
              //Recargamos la pagina con la url devuelta
              window.location = data;
            }
          }).error(function(data,textError,jqXHR) {
            alert('data :'+data+', textError :'+textError+', jqXHR :'+jqXHR);
          });
        }
      }
    </script><!-- ./Script -->
    <?php 
    if($id_pptal!=''){
        $cierre = cierre($id_pptal);
        if($cierre ==1){?> 
        <script>
            $("#btnEditar").prop("disabled", true);
            $("#btnEditar").attr("disabled", true);
            $("#btnGuardar").attr("disabled", true);
            $("#btnGuardarD").attr("disabled", true);     
            $("#sltConcepto").attr("disabled", true);
            $("#sltRubro").attr("disabled", true);
            $(".eliminar").css('display','none');
            $(".modificar").css('display','none');
            
        </script>
        <?php } else {
            if(!empty($recaudo)) {
              echo "<script>\n";
              echo "$(\"#btnGuardar\").attr('disabled',true);\n";
              echo "$(\"#btnImprimir,#btnEditar,#btnGuardarD\").attr('disabled',false);\n";
              echo "</script>\n";
            }else{
              echo "<script>\n";
              echo "$(\"#btnImprimir,#btnEditar,#btnGuardarD\").attr('disabled',true);\n";
              echo "$(\"#btnGuardar\").attr('disabled',false);\n";
              echo "</script>\n";
            }
        } 
    } else { 
       if(!empty($recaudo)) {
          echo "<script>\n";
          echo "$(\"#btnGuardar\").attr('disabled',true);\n";
          echo "$(\"#btnImprimir,#btnEditar,#btnGuardarD\").attr('disabled',false);\n";
          echo "</script>\n";
        }else{
          echo "<script>\n";
          echo "$(\"#btnImprimir,#btnEditar,#btnGuardarD\").attr('disabled',true);\n";
          echo "$(\"#btnGuardar\").attr('disabled',false);\n";
          echo "</script>\n";
        } 
    }
?>
  </body>
</html>
