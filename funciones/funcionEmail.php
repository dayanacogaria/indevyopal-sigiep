<?php

function enviarEmail($tercero, $indicador){
    require ('../Conexion/conexion.php');
    try {
        $sql = "SELECT u.email, CONCAT_WS(' ', u.nombre, u.apellido) AS TERCERO 
                FROM gt_usuarios u
                LEFT JOIN gf_tercero t ON u.documento = t.numeroidentificacion 
                WHERE t.id_unico = '$tercero'";
        $res = $mysqli->query($sql);
        $row = mysqli_fetch_row($res);

        $destinatario = $row[0];
        $nnomdest = $row[1];        
        $from = "informacion@sigiep.com";            
        $headers = "From:" . $from. "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $asunto = "Trámites en Línea | Concepto de Uso deL Suelo";    

       if ($indicador == 1){
        // mensaje de concepto generado  

         $p ="Hemos generado el <b>Concepto de Uso del Suelo</b> para tu establecimiento.";
         $p1="Por favor ingresa a Trámites en Línea y por la opción de menú: <b>CONCEPTO USO DEL SUELO / VER CONCEPTO</b>, podrás descargar el PDF del documento."; 
       
       }else{ 
         // mensaje de pago factura 

        $p="Su pago por Concepto de Uso del Suelo ya fue registrado en nuestro sistema.";
        $p1="Por favor ingrese al CENTRO DE SERVICIOS EN LÍNEA de la CÁMARA DE COMERCIO DE BUCARAMANGA y consulte el concepto generado. Tenga en cuenta que este proceso puede tardar 3 días hábiles.";
       }  

       
      $mensaje = '
        <html>
          <head>
             </head>
              <body>

            <tbody><tr>
            <td align="center" valign="top" style="margin: 0;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;    background-color: rgb(226, 224, 224)">                
                <table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="80%" >
                    <tbody><tr>
                        <td colspan="2" align="center" valign="top">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="color:#ffffff;background-image:url(http://tramitesbarbosa-sigiep.grupoaaaasesores.com/web/images/cabeza/cabeza.png);">
                                <tbody><tr>
                                    <td align="center" valign="top">
                                        <table border="0" cellpadding="0" cellspacing="0" width="90%" >
                                            <tbody><tr>
                                                <td align="center" valign="top" width="100">
                                                    <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                        <tbody><tr>
                                                            <td align="center" valign="top" >
                                                                <h1 style="color:#ffffff;line-height:100%;font-family:Helvetica,Arial,sans-serif;font-size:35px;font-weight:normal;margin-bottom:5px;text-align:center">Trámites en Línea</h1>
                                                                <span style="color:#205478"></span></td>
                                                        </tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center" valign="top">
                            <table border="0" cellpadding="30" cellspacing="0" width="90%">
                                <tbody><tr>
                                    <td valign="top">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tbody><tr>
                                                <td align="left"><h3 style="color:#5f5f5f;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left"> </h3>    
                                                <div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#5f5f5f">
                                                    <p>Hola, '.$nnomdest.'</p> 
                                                    <p>'.$p.'</p>
                                                    <p>'.$p1.'</p> 
                                                    <p>Hasta Otra Oportunidad.</p>
                                                    <small>Ingrese por aquí:</small>
                                                    <small>http://www.sintramites.com/</small></div></td>
                                            </tr>
                                        </tbody></table>
                                        </td>
                                </tr>
                            </tbody></table>                                    
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center" valign="top">
                            
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="color:#ffffff;background-image:url(http://tramitesbarbosa-sigiep.grupoaaaasesores.com/web/images/footer/pie.png)">
                                <tbody><tr>
                                    <td align="center" valign="top">                                        
                                        
                                        <table border="0" cellpadding="0" cellspacing="0" width="90%" style="background-image:url(http://tramitesbarbosa-sigiep.grupoaaaasesores.com/web/images/footer/footer.png);background-repeat:no-repeat;background-position:center" >
                                            <tbody><tr>
                                                <td align="center" valign="top" width="100" >
                                                    
                                                    <table border="0" cellpadding="10" cellspacing="0" width="100%" style="">
                                                            <tbody><tr>
                                                                <td align="center" valign="top" >
                                                                    <h1 style="color:#0046B1;line-height:100%;font-family:Helvetica,Arial,sans-serif;font-size:35px;font-weight:normal;margin-bottom:5px;text-align:center"></h1>
                                                                    <span style="color:#205478"></span></td>
                                                            </tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>
                                        </tbody></table>
                                        
                                    </td>
                                </tr>
                            </tbody></table>
                        </td>
                    </tr>
                </tbody></table>
                <table bgcolor="#E1E1E1" border="0" cellpadding="0" cellspacing="0" width="80%" >
                 <tbody><tr>
                  <td align="center" valign="top">                            
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tbody><tr>
                            <td align="center" valign="top">
                            </td>
                        </tr>
                    </tbody></table>
                   </td>
                  </tr>
              </tbody></table>
           </td>
        </tr>
    </tbody>
   </body>
 </html>';
   
    mail($destinatario,$asunto,$mensaje,$headers) ;
        
    } catch (Exception $e) {
        die($e->getMessage());
    }
}