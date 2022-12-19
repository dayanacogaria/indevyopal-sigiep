<?php 

require_once('Conexion/conexion.php'); 
require_once 'head.php'; 
//Variables de sesión para determinar el id del tercero que se está consultando y la url para regresar.
 
  //Captura de ID y consulta del resgistro correspondiente.
  $id_ter_comp = " ";
  if (isset($_GET["id_ter_comp"]))
  { 
    $id_ter_comp = (($_GET["id_ter_comp"]));

//Consulta general
    $queryTerceroComp ="SELECT  t.Id_Unico,
                                t.RazonSocial,
                                t.NumeroIdentificacion,
                                t.DigitoVerficacion,
                                ti.Id_Unico,
                                ti.Nombre,
                                s.Id_Unico,
                                s.nombre,
                                t.RepresentanteLegal,
                                ci.Id_Unico,
                                ci.Nombre,
                                tr.Id_Unico,
                                tr.Nombre,
                                t.Contacto,
                                te.Id_Unico,
                                te.Nombre,
                                tipen.Id_Unico,
                                tipen.Nombre,
                                ci.Departamento,
                                t.codigo_dane,
                                t.ruta_logo
                        FROM gf_tercero t 
                        LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico
                        LEFT JOIN gf_sucursal s ON t.Sucursal = s.Id_Unico
                        LEFT JOIN gf_tipo_regimen tr ON t.TipoRegimen = tr.Id_Unico
                        LEFT JOIN gf_tipo_empresa te ON t.TipoEmpresa = te.Id_Unico
                        LEFT JOIN gf_tipo_entidad tipen ON t.TipoEntidad = tipen.Id_Unico
                        LEFT JOIN gf_ciudad ci ON t.CiudadIdentificacion = ci.Id_Unico 
                        WHERE md5(t.Id_Unico) = '$id_ter_comp'";
  }

  $resultado = $mysqli->query($queryTerceroComp);
  $row = mysqli_fetch_row($resultado);

   $_SESSION['id_tercero'] = $row[0];
  $_SESSION['perfil'] = "J"; //Jurídica.
  $_SESSION['url'] = "modificar_TERCERO_COMPANIA.php?id_ter_comp=".(($_GET["id_ter_comp"]));
  $_SESSION['tipo_perfil']='Compañía';

  //Consultas para el listado de los diferentes combos correspondientes.
  //Tipo Identificación.
  $sqlTipoIden = "SELECT DISTINCT Id_Unico, Nombre 
  FROM gf_tipo_identificacion
  WHERE Id_Unico != '$row[4]' 
  ORDER BY Nombre ASC";
  $tipoIden = $mysqli->query($sqlTipoIden);

  //Sucursal.
  $sqlSucursal = "SELECT DISTINCT Id_Unico, Nombre 
  FROM gf_sucursal
  WHERE Id_Unico != '$row[6]' 
  ORDER BY Nombre ASC";
  $sucursal = $mysqli->query($sqlSucursal);

  //Tipo Régimen.
  $sqlTipoReg = "SELECT DISTINCT Id_Unico, Nombre 
  FROM gf_tipo_regimen
  WHERE Id_Unico != '$row[11]'
  ORDER BY Nombre ASC";
  $tipoReg = $mysqli->query($sqlTipoReg);

  //Tipo Empresa.
  if(empty($row[14])){
  $sqlTipoEmp = "SELECT DISTINCT Id_Unico, Nombre 
  FROM gf_tipo_empresa 
  ORDER BY Nombre ASC";
  $tipoEmp = $mysqli->query($sqlTipoEmp);    
  }else {
  $sqlTipoEmp = "SELECT DISTINCT Id_Unico, Nombre 
  FROM gf_tipo_empresa
  WHERE Id_Unico != '$row[14]'
  ORDER BY Nombre ASC";
  $tipoEmp = $mysqli->query($sqlTipoEmp);
  }
  if(empty($row[16])){
  //Tipo Entidad.
  $sqlTipoEnt = "SELECT DISTINCT Id_Unico, Nombre 
  FROM gf_tipo_entidad 
  ORDER BY Nombre ASC";
  $tipoEnt = $mysqli->query($sqlTipoEnt);
  } else {
  $sqlTipoEnt = "SELECT DISTINCT Id_Unico, Nombre 
  FROM gf_tipo_entidad
  WHERE Id_Unico != '$row[16]'
  ORDER BY Nombre ASC";
  $tipoEnt = $mysqli->query($sqlTipoEnt);   
  }
  if(empty($row[8])){
  //Representante Legal.
  $sqlReprLeg = "SELECT DISTINCT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt  
  WHERE t.TipoIdentificacion = ti.Id_Unico
  AND t.Id_Unico = pt.Tercero 
  AND pt.Perfil != 1 
  ORDER BY t.NombreUno ASC";
  $repreLegal = $mysqli->query($sqlReprLeg);
  }else {
    $sqlReprLeg = "SELECT DISTINCT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt  
  WHERE t.TipoIdentificacion = ti.Id_Unico
  AND t.Id_Unico = pt.Tercero 
  AND pt.Perfil != 1
  AND t.Id_Unico != '$row[8]'
  ORDER BY t.NombreUno ASC";
  $repreLegal = $mysqli->query($sqlReprLeg);  
  }
  
  if(empty($row[13])){
     $sqlContacto = "SELECT DISTINCT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt     
  WHERE t.TipoIdentificacion = ti.Id_Unico 
  AND t.Id_Unico = pt.Tercero 
  AND pt.Perfil = 10 
  ORDER BY t.NombreUno ASC";
  $contacto = $mysqli->query($sqlContacto); 
  } else {
  //Contacto.
  $sqlContacto = "SELECT DISTINCT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt     
  WHERE t.TipoIdentificacion = ti.Id_Unico 
  AND t.Id_Unico = pt.Tercero 
  AND pt.Perfil = 10
  AND t.Id_Unico != '$row[13]'
  ORDER BY t.NombreUno ASC";
  $contacto = $mysqli->query($sqlContacto);
  //Fin de las consultas para combos.
  }
  
?>

<!-- Script para calcular el dígito de verificación. -->
<script type="text/javascript">
    function CalcularDv()
{ 
 var arreglo, x, y, z, i, nit1, dv1;
 nit1=document.form.noIdent.value;
  if (isNaN(nit1))
  {
  document.form.digitVerif.value="X";
      alert('Número del Nit no valido, ingrese un número sin puntos, ni comas, ni guiones, ni espacios');   
  } else {
  arreglo = new Array(16); 
  x=0 ; y=0 ; z=nit1.length ;
  arreglo[1]=3;   arreglo[2]=7;   arreglo[3]=13; 
  arreglo[4]=17;  arreglo[5]=19;  arreglo[6]=23;
  arreglo[7]=29;  arreglo[8]=37;  arreglo[9]=41;
  arreglo[10]=43; arreglo[11]=47; arreglo[12]=53;  
  arreglo[13]=59; arreglo[14]=67; arreglo[15]=71;
  for(i=0 ; i<z ; i++)
  { 
   y=(nit1.substr(i,1));
     x+=(y*arreglo[z-i]);
  } 
  y=x%11
  if (y > 1){ dv1=11-y; } else { dv1=y; }
  document.form.digitVerif.value=dv1;
  }
}
  </script>



<title>Modificar Compañía</title>
</head>
<body>

</div>

<div class="container-fluid text-center">
  <div class="row content" style="margin-bottom:-20px">
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top: -20px">

      <h2 align="center" class="tituloform">Modificar Compañía</h2>

      <div class="client-form contenedorForma" style="margin-top:-5px">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_TERCERO_COMPANIAJson.php">

          <p align="center" class="parrafoO" style="margin-bottom:-0.00005em">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>

            <input type="hidden" name="id" value="<?php echo $row[0];?>">

            <div class="form-group form-inline">

                            <label for="noIdent" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                            
                            <select name="tipoIdent" id="tipoIdent" class="form-control col-sm-5" style="height: 33px;width:113px" title="Tipo Identificación" required>
                                <option value="<?php echo $row[4]; ?>"><?php echo $row[5]; ?></option>
                                <?php while ($ma = mysqli_fetch_assoc($tipoIden)) { ?>
                                    <option value="<?php echo $ma["Id_Unico"]; ?>">
                                        <?php echo ucwords((strtolower($ma["Nombre"]))); ?>
                                    </option>
                                <?php } ?>
                            </select>
                            
                            <span class="col-sm-1" style="width:1px; margin-top:8px;"></span>
                            
                            <input type="text" name="noIdent" id="noIdent" class="form-control col-sm-5" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event,'num')" placeholder="Número" style="width:95px" style="height: 30px" value="<?php echo $row[2]; ?>" required onblur="CalcularDv();return existente()" />

                            <span class="col-sm-1" style="width:1px; margin-top:8px;"><strong> - </strong></span>

                            <input type="text" value="<?php echo $row[3]; ?>" name="digitVerif" id="digitVerif" class="form-control " style="width:30px" maxlength="1" placeholder="0" title="Dígito de verificación" onkeypress="return txtValida(event,'num')" placeholder="" readonly="" style="height: 30px"/>

                        </div>
              

            <div class="form-group" style="margin-top: -20px; ">
              <label for="sucursal" class="col-sm-5 control-label"><strong class="obligado">*</strong>Sucursal:</label>
              <select name="sucursal" id="sucursal" class="form-control" title="Ingrese el tipo de identificación" required>
                <option value="<?php echo $row[6];?>"><?php echo  ($row[7]);?></option>
          <?php while($rowS = mysqli_fetch_row($sucursal))
                {  ?>
                <option value="<?php echo $rowS[0] ?>"><?php echo ucwords( (strtolower($rowS[1]))); ?></option>
          <?php
                }  ?>
              </select> 
            </div>


            <div class="form-group" style="margin-top: -20px; ">
              <label for="razoSoci" class="col-sm-5 control-label"><strong class="obligado">*</strong>Razón Social:</label>
                <input type="text" name="razoSoci" id="razoSoci" class="form-control" maxlength="500" title="Ingrese la razón social" value="<?php echo  ($row[1]);?>" onkeypress="return txtValida(event,'car')"  onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Razón Social" required>
               
            </div>


            <div class="form-group" style="margin-top: -20px; ">
              <label for="tipoReg" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tipo Régimen:</label>
              <select name="tipoReg" id="tipoReg" class="form-control" title="Ingrese el tipo de régimen" required>
                  <option value="<?php echo $row[11];?>"><?php echo ucwords(strtolower($row[12]));?></option>
          <?php while($rowTR = mysqli_fetch_row($tipoReg))
                {  ?>
                <option value="<?php echo $rowTR[0] ?>"><?php echo ucwords( (strtolower($rowTR[1]))); ?></option>
          <?php
                }  ?>
              </select> 
            </div>



            <div class="form-group" style="margin-top: -20px; ">
              <label for="tipoEmp" class="col-sm-5 control-label"><strong class="obligado"></strong>Tipo Empresa:</label>
              <select name="tipoEmp" id="tipoEmp" class="form-control" title="Ingrese el tipo de empresa" >
                 <?php if (empty($row[14])) { ?>
                  <option value="">-</option>
                 <?php } else { ?>
                  <option value="<?php echo $row[14];?>"><?php echo  ($row[15]);?></option>
                  <option value="">-</option>
                 <?php } ?>
          <?php while($rowTE = mysqli_fetch_row($tipoEmp))
                {  ?>
                <option value="<?php echo $rowTE[0] ?>"><?php echo ucwords( (strtolower($rowTE[1]))); ?></option>
                
                    <?php
                }  ?>
                
              </select> 
            </div>



            <div class="form-group" style="margin-top: -20px; ">
              <label for="tipoEnt" class="col-sm-5 control-label"><strong class="obligado"></strong>Tipo Entidad:</label>
              <select name="tipoEnt" id="tipoEnt" class="form-control" title="Ingrese el tipo de entidad" >
                <?php if (empty($row[16])) { ?>
                  <option value="">-</option>
                 <?php } else { ?>
                  <option value="<?php echo $row[16];?>"><?php echo ucwords(strtolower($row[17]))?></option>
                  <option value="">-</option>
                 <?php } ?>
  
                
          <?php while($rowTEn = mysqli_fetch_row($tipoEnt))
                {  ?>
                <option value="<?php echo $rowTEn[0] ?>"><?php echo ucwords( (strtolower($rowTEn[1]))); ?></option>
          <?php
                }  ?>
                
              </select> 
            </div>

             <div class="form-group" style="margin-top: -20px; ">
              <label for="repreLegal" class="col-sm-5 control-label"><strong class="obligado"></strong>Representante Legal:</label>
              <select name="repreLegal" id="repreLegal" class="form-control" title="Ingrese el representante legal" >
                <?php
                
                $sqlElReprLeg = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                  FROM gf_tercero t, gf_tipo_identificacion ti  
                  WHERE t.TipoIdentificacion = ti.Id_Unico
                  AND t.Id_Unico = '$row[8]'";
                  $elReprLeg = $mysqli->query($sqlElReprLeg);
                  $rowElReprLeg = mysqli_fetch_row($elReprLeg);
                  
                  if(empty($row[8])) { 
              ?>
                  <option value="">-</option>  
                  <?php } else { ?>
                <option value="<?php echo $rowElReprLeg[0] ?>">
                  <?php echo ucwords( (strtolower($rowElReprLeg[1]." ".$rowElReprLeg[2]." ".$rowElReprLeg[3]." ".$rowElReprLeg[4]." (".$rowElReprLeg[6].", ".$rowElReprLeg[5].")"))); ?>
                </option>
                  <?php } ?>
          <?php while($rowRL = mysqli_fetch_row($repreLegal))
                {  ?>
                <option value="<?php echo $rowRL[0] ?>">
                <?php echo ucwords( (strtolower($rowRL[1]." ".$rowRL[2]." ".$rowRL[3]." ".$rowRL[4]." (".$rowRL[6].", ".$rowRL[5].")"))); ?>
                </option>
          <?php
                }  ?>
                <option value="">-</option>
              </select> 
            </div>


<!--  Inicio combos dinámicos -->

<div class="form-group form-inline" style="margin-top: -20px">
                            <label for="depto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ubicación:</label>     
                            
                            <div class="classDepto">
                                
                                <select name="depto" id="depto" class="form-control col-sm-5" style="height: 20%;width:170px;margin-bottom: -10px" title="Seleccione Departamento" required>
                                </select>
                                <script type="text/javascript">
                                    $(document).ready(function(){                   
                                        $.ajax({       
                                            data: {"id_ciudad_depto": "<?php echo $row[18];?>"},
                                            type: "POST",
                                            url: "MDepartamento.php",
                                            success: function(response){
                                                $('.classDepto select').html(response).fadeIn();
                                            }
                                        });
                                    });
                                </script>
                            </div>
                            
                            <span class="col-sm-1" style="width:1px"></span>
                            
                            <div class="ClassCiudad">
                                <select name="ciudad" style="height: 24%;width:100px" id="ciudad" class="form-control" title="Seleccione Ciudad" required>
                                    <option value="">Ciudad</option>
                                </select>
                                <script type="text/javascript">
                                    $(document).ready(function()
                                    {
                                        var cambio = 0;
                    //Este evento change funciona cuando se cambia de departamento.
                                        $(".classDepto select").change(function()
                                        {
                                            cambio = 1;
                                            var form_data =
                                            {
                                                is_ajax: 1,
                                                id_depto: +$(".classDepto select").val()
                                        };
                                        $.ajax({
                                            type: "POST",
                                            url: "Ciudad.php",
                                            data: form_data,
                                            success: function(response)
                                            {
                                                $('.ClassCiudad select').html(response).fadeIn();
                                            }
                                        });
                                    });

                    // Se eliminó el evento click y el select caragará junto con la página.
                    //$(".ClassCiudad select").click(function()
                    //{
                                     if (cambio == 0) 
                                    {
                                    //cambio = 1;
                                        $.ajax({
                                            data:{"id_ciudad":"<?php echo $row[9];?>","id_ciudad_depto":"<?php echo $row[18];?>"},
                                            type: "POST",
                                            url: "MCiudad.php",
                                            success: function(response)
                                            {
                                            $('.ClassCiudad select').html(response).fadeIn();
                                            }
                                        });
                    
                                    }
                    //});

                                });
                            </script>
                            </div>
                        </div>

<!--  Fin combos dinámicos  -->


            <div class="form-group" style="margin-top: -20px; ">
              <label for="contacto" class="col-sm-5 control-label">Contacto:</label>
              <select name="contacto" id="contacto" class="form-control" title="Ingrese el contacto">
                    <?php
                        if(!empty($row[13]))
                        {

                          $sqlElContacto = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                          FROM gf_tercero t, gf_tipo_identificacion ti  
                          WHERE t.TipoIdentificacion = ti.Id_Unico
                          AND t.Id_Unico = '$row[13]'";
                          $elContacto = $mysqli->query($sqlElContacto);
                          $rowElCon = mysqli_fetch_row($elContacto);
                           echo '<option value="'.$row[13].'">'.ucwords(strtolower($rowElCon[1]." ".$rowElCon[2]." ".$rowElCon[3]." ".$rowElCon[4]." (".$rowElCon[6].", ".$rowElCon[5].")")).'</option>'; 
                            $sqlContactos = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                            FROM gf_tercero t
                            LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico 
                            LEFT JOIN gf_perfil_tercero pt   ON   t.Id_Unico = pt.Tercero 
                            WHERE pt.Perfil = 10
                            AND t.Id_Unico != '$row[13]'
                            ORDER BY t.NombreUno ASC";
                            $contactos = $mysqli->query($sqlContactos);
                            while($con = mysqli_fetch_row($contactos)){
                              echo '<option value="'.$con[0].'">'.$con[1].' '.$con[2].' '.$con[3].' '.$con[4].'('.$con[5].' - '.$con[6].')'.'</option>';
                            } 
                            echo '<option value=""></option>';
                        }
                      
                            else {
                              echo '<option value="">-</option>';
                              $sqlContactos = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                              FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt     
                              WHERE t.TipoIdentificacion = ti.Id_Unico 
                              AND t.Id_Unico = pt.Tercero 
                              AND pt.Perfil = 10
                              ORDER BY t.NombreUno ASC";
                              $contactos = $mysqli->query($sqlContactos);
                              while($con = mysqli_fetch_row($contactos)){
                                echo '<option value="'.$con[0].'">'.$con[1].' '.$con[2].' '.$con[3].' '.$con[4].'('.$con[5].' - '.$con[6].')'.'</option>';
                              }
                          }
                           ?>

              </select> 
            </div>
            <div class="form-group" style="margin-top: -20px;">
              <label for="codigo" class="col-sm-5 control-label"><strong class="obligado"></strong>Código DANE:</label>
              <?php if(empty($row[19])) { ?>
              <input type="text" name="codigo" id="codigo" class="form-control" maxlength="500" title="Ingrese el código DANE" onkeypress="return txtValida(event,'num_car')"  placeholder="Código DANE" onkeyup="javascript:this.value=this.value.toUpperCase();">
              <?php }  else { ?>
              <input type="text" name="codigo" id="codigo" class="form-control" maxlength="500" title="Ingrese el código DANE" onkeypress="return txtValida(event,'num_car')"  placeholder="Código DANE" onkeyup="javascript:this.value=this.value.toUpperCase();"  value="<?php echo $row[19];?>">
              <?php } ?>
            </div>
            <div class="form-group" style="margin-top: -20px">
              <label for="flLogo" class="col-sm-5 control-label">Logo:</label>
              <input type="file" name="flLogo" id="flLogo" class="form-control" accept="image/*">
            </div>
            <input type="hidden"  name="txtLogo" id="txtLogo" value="<?php echo $row[20]; ?>"/>
            <div class="form-group" style="margin-top:-10px;">
             <label for="no" class="col-sm-5 control-label"></label>
             <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: -10px; margin-left: 0px;">Guardar</button>
            </div>


            <input type="hidden" name="MM_insert" >
          </form>
        </div>
         
   
    </div> <!-- Cierra clase col-sm-7 text-left -->

    <div class="col-sm-7 col-sm-3" style="margin-top:-20px">

        <h2 class="titulo" align="center">Información Adicional</h2>

        <div align="center">
        
            <a href="GF_DIRECCION_TERCERO.php"><button class="btn btnInfo btn-primary">DIRECCIÓN</button></a><br/>

           <a href="GF_CUENTA_BANCARIA_TERCERO.php"><button class="btn btnInfo btn-primary">CUENTA BANCARIA</button></a><br/>

           <a href="GF_TIPO_ACTIVIDAD_TERCERO.php"><button class="btn btnInfo btn-primary">TIPO ACTIVIDAD</button></a><br/>

          <a href="GF_TELEFONO.php"><button class="btn btnInfo btn-primary">TELÉFONO</button></a><br/>

           <a href="GF_CONDICION_TERCERO.php"><button class="btn btnInfo btn-primary">CONDICIÓN</button></a><br/>

          <a href="registrar_TERCERO_CONTACTO_NATURAL.php" class="btn btnInfo btn-primary">CONTACTO</a><br/>
          <a href="GF_PERFIL_CONDICION.php" class="btn btnInfo btn-primary">PERFIL CONDICIÓN</a>
        </div>

      </div>

  </div> <!-- Cierra clase row content -->
</div> <!-- Cierra clase container-fluid text-center -->
<?php require_once 'footer.php'; ?>

</body>
</html>