<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
        <link rel="shortcut icon" href="../../Media/Escudo.png" />
        <link rel="stylesheet" type="text/css" href="CSS/Estilos.css">
        <title>Consejería de Agricultura, Ganadería, Pesca y Aguas.</title>
    </head>
    <body>
        <div class="iFrameHeader"><?php include('../iFrame/Header.php'); ?></div>
        <div class="iFrameMenu"><?php include('../iFrame/Menu.php'); ?></div>
        <div class="central">
            <h3>Listado de Explotaciones con datos totales de la Comunidad:</h3>
            <?php                                                                   // Se empleará la misma metodología en --->TODAS LAS PÁGINAS<---:
                $conexion = new mysqli('localhost', 'root', '');                    // Primero se comprueba la conexion
                $nombreBDD = "ganaderia";                                           // Luego la Base de Datos
                //-----------------------------------------------------------       // Luego las tablas
                function CompruebaTabla($conexionUsar, $tablaUsar, $bddUsar){       // Por último, en cada consulta, se comprueba que no vuelva
                    $sentenciaTabla = "select * from " . $tablaUsar;                // vacía ni con errores, y en caso de haberlos, se muestran por pantalla.
                        $valor = true;
                        $conexionUsar->select_db($bddUsar);
                        $queryComprobar = mysqli_query($conexionUsar, $sentenciaTabla);
                        if(!$queryComprobar){
                            echo "<h3 style=\"margin-left: 1%\">ERROR: No se encuentra la tabla " . $tablaUsar . "</h3>";
                            echo "<h3 style=\"margin-left: 1%\">Base de Datos INCOMPLETA. Se debe realizar una reinstalación.</h3>";
                            $valor = false;
                        }
                        return($valor);
                }
                //-----------------------------------------------------------
                if($conexion->connect_error){
                    echo "<h3 style=\"margin-left: 1%\">Error al intentar conectar con el servidor</h3>";
                    echo "<h4 style=\"margin-left: 1%\">ERROR: </h4>" . mysqli_error($conexion);
                }
                else{
                    $conexion->set_charset('UTF-8');
                    $compruebaBDD = mysqli_select_db($conexion, $nombreBDD);
                    if(empty($compruebaBDD)){
                        echo "<h3 style=\"margin-left: 1%\">ERROR: Base de datos no encontrada.</h3>";
                    }
                    else{
                        $continuar = false;                                                 // Booleano empleado para poder detener el proceso siguiente
                        if(CompruebaTabla($conexion, "gan_datosganaderia", $nombreBDD)){    // en caso de que se haya encontrado un error.
                            if(CompruebaTabla($conexion, "gan_datosgenerales", $nombreBDD)){
                                if(CompruebaTabla($conexion, "gan_islas", $nombreBDD)){
                                    if(CompruebaTabla($conexion, "gan_municipios", $nombreBDD)){
                                        if(CompruebaTabla($conexion, "gan_tipoexplotaciones", $nombreBDD)){
                                            if(CompruebaTabla($conexion, "gan_tipoganaderia", $nombreBDD)){
                                                $continuar = true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        
                        if($continuar){
                                                                                                                                //Consulta empleada para obtener la lista de Explotaciones Generales.
                            $generalSentencia = "select TIE.TipoExplotacion, sum(DGEN.NExplotaciones) as NumeroExplotaciones  
                                                 from gan_datosgenerales as DGEN 
                                                 join gan_tipoexplotaciones as TIE on TIE.IdTipo = DGEN.idTipo 
                                                 group by TIE.TipoExplotacion";
                            $ejecutarGeneral = mysqli_query($conexion, $generalSentencia);
                            if(!$ejecutarGeneral){                                                                  //Si no se ejecuta de forma limpia la consulta,
                                echo "<h3 style=\"margin-left: 1%\">Error al realizar la consulta.</h3>";           //se muestra el error obtenido.
                                echo "<h4 style=\"margin-left: 1%\">ERROR: " . mysqli_error($conexion) . "</h4>"; 
                           }
                            else{                                                       //Si no se han obtenido errores, se guarda el contenido de la consulta.
                                while ($row = mysqli_fetch_assoc($ejecutarGeneral)){
                                     $tablaGeneral[] = $row;
                                }

                               if(count($tablaGeneral) == 0){       //Si se ha devuelto una consulta vacía significa que no han concordado los datos con los campos.
                                   echo "<h3 style=\"margin-left: 1%\">ERROR: No se ha encontrado la información especificada. Puede que se haya modificado la base de datos.</h3>";
                               }
                               else{
                                    $totalExploGen = 0; 
                                    echo "<table>";     
                                    echo "<caption><b>Explotaciones en General</b></caption>";
                                    echo "<tr>";
                                    echo "<td>Tipo de Explotación</td>";
                                    echo "<td>Número de Explotaciones</td>";
                                    echo "</tr>";
                                   for($i = 0; $i < count($tablaGeneral); $i++){
                                       echo "<tr>";
                                       echo "<td>" . $tablaGeneral[$i]['TipoExplotacion'] . "</td>";
                                       echo "<td>" . $tablaGeneral[$i]['NumeroExplotaciones'] . "</td>";
                                       echo "</tr>";
                                       $totalExploGen += $tablaGeneral[$i]['NumeroExplotaciones'];
                                   }
                                   echo "<tr>";
                                   echo "<td> ⠀</td><td> ⠀</td>";
                                   echo "</tr>";
                                   echo "<tr>";
                                   echo "<td style=\"text-align: right;\">Total Explotaciones:</td><td>" . $totalExploGen . "</td>";
                                   echo "</tr>";
                                   echo "</table>";
                               }
                            }
                            //----------------------------------------------------------------------------------------------------------------------------------------------
                                                                            // MISMO PROCESO que con la primera consulta (General)
                            $ganaderiaSentencia = "select TIG.TipoGanaderia, sum(DGAN.NExplotacionesGanaderas) as 'NumeroExplotaciones', sum(DGAN.Ncabezas) as 'TotalCabezas' 
                                                   from gan_datosganaderia as DGAN 
                                                   join gan_tipoganaderia as TIG on DGAN.idTipo = TIG.IdTipo
                                                   group by TIG.TipoGanaderia";
                            $ejecutarGanaderia = mysqli_query($conexion, $ganaderiaSentencia);
                            if(!$ejecutarGanaderia){
                                echo "<h3 style=\"margin-left: 1%\">Error al realizar la consulta.</h3>";
                                echo "<h4 style=\"margin-left: 1%\">ERROR: " . mysqli_error($conexion) . "</h4>";
                            }
                            else{
                                while ($row = mysqli_fetch_assoc($ejecutarGanaderia)){
                                     $tablaGanaderia[] = $row;
                                }
                                
                                if(count($tablaGanaderia) == 0){
                                    echo "<h3 style=\"margin-left: 1%\">ERROR: No se ha encontrado la información especificada. Puede que se haya modificado la base de datos.</h3>";
                                }
                                else{
                                    $totalCabezas = 0;
                                    $totalExploGan = 0;
                                    echo "<table>";
                                    echo "<caption><b>Explotaciones Ganaderas</b></caption>";
                                    echo "<tr>";
                                    echo "<td>Tipo de Ganadería</td>";
                                    echo "<td>Número de Explotaciones</td>";
                                    echo "<td>Número de cabezas</td>";
                                    echo "</tr>";
                                   for($i = 0; $i < count($tablaGanaderia); $i++){
                                       echo "<tr>";
                                       echo "<td>" . $tablaGanaderia[$i]['TipoGanaderia'] . "</td>";
                                       echo "<td>" . $tablaGanaderia[$i]['NumeroExplotaciones'] . "</td>";
                                       echo "<td>" . $tablaGanaderia[$i]['TotalCabezas'] . "</td>";
                                       echo "</tr>";
                                       $totalCabezas += $tablaGanaderia[$i]['TotalCabezas'];
                                       $totalExploGan += $tablaGanaderia[$i]['NumeroExplotaciones'];
                                   }
                                   echo "<tr>";
                                   echo "<td> ⠀</td><td> ⠀</td><td> ⠀</td>";
                                   echo "</tr>";
                                   echo "<tr>";
                                   echo "<td style=\"text-align: right;\">Totales:</td><td>" . $totalExploGan . "</td><td>" . $totalCabezas . "</td>";
                                   echo "</tr>";
                                   echo "</table>";
                                }
                            }
                        }
                    }
                }
                mysqli_close($conexion);
            ?>
        </div>
        <div class="iFrameFooter"><?php include('../iFrame/Footer.php'); ?></div>
    </body>
</html>

