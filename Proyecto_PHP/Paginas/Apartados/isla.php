<?php
    function CompruebaTabla($conexionUsar, $tablaUsar, $bddUsar){
                    $sentenciaTabla = "select * from " . $tablaUsar;
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
?>
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
            <?php 
                $islaSeleccionada = "";
                if($_SERVER['REQUEST_METHOD'] == 'POST'){       // Se controla que se muestre el contenido si se accede a la página
                                                                // desde un Submit (Isla) (Con método POST en este caso)
                    $conexion = new mysqli('localhost', 'root', '');
                    $conexion->set_charset('UTF-8');                                // De resto, se continúa empleando la MISMA METODOLOGÍA
                    $nombreBDD = "ganaderia";                                       // para comprobar toda la Base de Datos y la conexión.
                    $bddComprobada = false;
                    $continuar = false;
                    $totalExploGen = 0; // Estas variables almacenan el total de la Comunidad Autónoma para
                    $totalExploGan = 0; // luego poder mostrar 
                    $totalCabezas = 0;
                    $compruebaBDD = mysqli_select_db($conexion, $nombreBDD);
                    if(empty($compruebaBDD)){
                        echo "<h3 style=\"margin-left: 1%\">ERROR: Base de datos no encontrada.</h3>";
                    }
                    else{
                        if(CompruebaTabla($conexion, "gan_datosganaderia", $nombreBDD)){
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
                        
                        // Proceso para almacenar el total de la Tabla General de la Comunidad Autónoma y la Tabla de Ganadería
                        if($continuar == true){
                            $bddComprobada = true;
                            
                            $generalSentencia = "select sum(DGEN.NExplotaciones) as NumeroExplotaciones 
                                                 from gan_datosgenerales as DGEN 
                                                 join gan_tipoexplotaciones as TIE on TIE.IdTipo = DGEN.idTipo 
                                                 group by TIE.TipoExplotacion";
                            $ejecutarGeneral = mysqli_query($conexion, $generalSentencia);
                            
                            if(!$ejecutarGeneral){
                                echo "<h3 style=\"margin-left: 1%\">Error al realizar la consulta.</h3>";
                                echo "<h4 style=\"margin-left: 1%\">ERROR: " . mysqli_error($conexion) . "</h4>";
                                $continuar = false;
                            }
                            else{
                                while ($row = mysqli_fetch_assoc($ejecutarGeneral)){
                                     $tablaGeneral[] = $row;
                                }
 
                               if(count($tablaGeneral) == 0){
                                   echo "<h3 style=\"margin-left: 1%\">ERROR: No se ha encontrado la información especificada. Puede que se haya modificado la base de datos.</h3>";
                                   $continuar = false;
                               }
                               else{
                                    
                                    for($i = 0; $i < count($tablaGeneral); $i++){
                                       $totalExploGen += utf8_encode($tablaGeneral[$i]['NumeroExplotaciones']);
                                   }
                                   $continuar = true;
                               } 
                            }
                            //---------------------------------------------------------------------------------------------------------------------------
                            $ganaderiaSentencia = "select sum(DGAN.NExplotacionesGanaderas) as 'NumeroExplotaciones', sum(DGAN.Ncabezas) as 'TotalCabezas'
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
                                    $continuar = false;
                                }
                                else{
                                   for($i = 0; $i < count($tablaGanaderia); $i++){
                                       $totalCabezas += utf8_encode($tablaGanaderia[$i]['TotalCabezas']);
                                       $totalExploGan += utf8_encode($tablaGanaderia[$i]['NumeroExplotaciones']);
                                   }
                                   $continuar = true;
                                }
                            }
                        }
                    }
                    
                    //  Se almacena qué Isla fue escogida en el Menú
                    if(isset($_POST['subElHierro'])){
                        $islaSeleccionada = trim($_POST['subElHierro']);
                    }
                    else if(isset($_POST['subFuerteventura'])){
                        $islaSeleccionada = $_POST['subFuerteventura'];
                    }
                    else if(isset($_POST['subGranCanaria'])){
                        $islaSeleccionada = $_POST['subGranCanaria'];
                    }
                    else if(isset($_POST['subLaGomera'])){
                        $islaSeleccionada = $_POST['subLaGomera'];
                    }
                    else if(isset($_POST['subLaPalma'])){
                        $islaSeleccionada = $_POST['subLaPalma'];
                    }
                    else if(isset($_POST['subLanzarote'])){
                        $islaSeleccionada = $_POST['subLanzarote'];
                    }
                    else if(isset($_POST['subTenerife'])){
                        $islaSeleccionada = $_POST['subTenerife'];
                    }
                    
                    //Se muestra por pantalla qué Isla se ha escogido, y se añade un Link a Wikipedia que muestra información de la Isla escogida.
                    if($islaSeleccionada != ""){
                        $islaWiki = $islaSeleccionada;
                        for($i = 0; $i < strlen($islaWiki); $i++){
                            if($islaWiki[$i] == " "){
                                $islaWiki[$i] = "_";
                            }
                        }
                        echo "<h2>Información sobre la isla de " . $islaSeleccionada . " | <a target=\"_blank\" href=\"https://es.wikipedia.org/wiki/" . $islaWiki ."\">Información en profundidad<img id=\"link\" src=\"../../Media/iconoLink.svg\" /></a></h2>";
                    }
                    
                    // Si no ha habido errores, se hace la consulta para mostrar la Tabla General y la Tabla de Ganadería de la Isla escogida.
                    if($bddComprobada && $continuar){
                        $islaSentencia = "select TIE.TipoExplotacion, sum(DGEN.NExplotaciones) as NumeroExplotaciones 
                                          from gan_datosgenerales as DGEN 
                                          join gan_tipoexplotaciones as TIE on TIE.IdTipo = DGEN.idTipo
                                          join gan_municipios as MUNI on DGEN.idMunicipio = MUNI.IdMunicipio
                                          join gan_islas as ISL on MUNI.IdIsla = ISL.IdIsla
                                          where ISL.Isla = '" . strtoupper($islaSeleccionada) . "'
                                          group by TIE.TipoExplotacion";
                        
                        $ejecutarConsulta = mysqli_query($conexion, $islaSentencia);
                        if(!$ejecutarConsulta){
                            echo "<h3 style=\"margin-left: 1%\">Error al realizar la consulta.</h3>";
                            echo "<h4 style=\"margin-left: 1%\">ERROR: " . mysqli_error($conexion) . "</h4>";
                        }
                        else{
                            while ($row = mysqli_fetch_assoc($ejecutarConsulta)){
                                     $tablaGeneralIsla[] = $row;
                                }
                                $totalExploGenIsla = 0;
                            
                            if(count($tablaGeneralIsla) == 0){
                                   echo "<h3 style=\"margin-left: 1%\">ERROR: No se ha encontrado la información especificada. Puede que se haya modificado la base de datos.</h3>";
                               }
                               else{
                                   echo "<table>";
                                   echo "<caption><b>Explotaciones en General de la Isla de " . $islaSeleccionada ."</b></caption>";
                                    echo "<tr>";
                                    echo "<td>Tipo de Explotación</td>";
                                    echo "<td>Número de Explotaciones</td>";
                                    echo "</tr>";
                                   for($i = 0; $i < count($tablaGeneralIsla); $i++){
                                       echo "<tr>";
                                       echo "<td>" . $tablaGeneralIsla[$i]['TipoExplotacion'] . "</td>";
                                       echo "<td>" . $tablaGeneralIsla[$i]['NumeroExplotaciones'] . "</td>";
                                       echo "</tr>";
                                       $totalExploGenIsla += $tablaGeneralIsla[$i]['NumeroExplotaciones'];
                                   }
                                   $porcentajeGenIsla = ($totalExploGenIsla / $totalExploGen) * 100;
                                   echo "<tr>";
                                   echo "<td style=\"text-align: right;\">Total:</td><td>" . $totalExploGenIsla . "</td>";
                                   echo "</tr>";
                                   echo "<tr>";
                                   echo "<td style=\"text-align: right;\">Porcentaje respecto a la Comunidad:</td><td>" . number_format($porcentajeGenIsla, 2) . "%</td>";
                                   echo "</tr>";
                                   echo "</table>";
                               }
                        }
                        //---------------------------------------------------------------------------------------------------------------------------------------------
                        $islaGanSentencia = "select TIG.TipoGanaderia, sum(DGAN.NExplotacionesGanaderas) as 'NumeroExplotaciones', sum(DGAN.Ncabezas) as 'TotalCabezas'
                                             from gan_datosganaderia as DGAN 
                                             join gan_tipoganaderia as TIG on DGAN.idTipo = TIG.IdTipo
                                             join gan_municipios as MUNI on DGAN.idMunicipio = MUNI.IdMunicipio
                                             join gan_islas as ISL on MUNI.IdIsla = ISL.IdIsla
                                             where ISL.Isla = '" . strtoupper($islaSeleccionada) . "'
                                             group by TIG.TipoGanaderia";
                            $ejecutarGanaderiaIsla = mysqli_query($conexion, $islaGanSentencia);
                            if(!$ejecutarGanaderiaIsla){
                                echo "<h3 style=\"margin-left: 1%\">Error al realizar la consulta.</h3>";
                                echo "<h4 style=\"margin-left: 1%\">ERROR: " . mysqli_error($conexion) . "</h4>";
                            }
                            else{
                                while ($row = mysqli_fetch_assoc($ejecutarGanaderiaIsla)){
                                     $tablaGanaderiaIsla[] = $row;
                                }
                                
                                if(count($tablaGanaderiaIsla) == 0){
                                    echo "<h3 style=\"margin-left: 1%\">ERROR: No se ha encontrado la información especificada. Puede que se haya modificado la base de datos.</h3>";
                                }
                                else{
                                    $totalCabezasIsla = 0;
                                    $totalExploGanIsla = 0;
                                    echo "<table>";
                                    echo "<caption><b>Explotaciones Ganaderas de la Isla de " . $islaSeleccionada ."</b></caption>";
                                    echo "<tr>";
                                    echo "<td>Tipo de Ganadería</td>";
                                    echo "<td>Número de Explotaciones</td>";
                                    echo "<td>Número de cabezas</td>";
                                    echo "</tr>";
                                   for($i = 0; $i < count($tablaGanaderiaIsla); $i++){
                                       echo "<tr>";
                                       echo "<td>" . $tablaGanaderiaIsla[$i]['TipoGanaderia'] . "</td>";
                                       echo "<td>" . $tablaGanaderiaIsla[$i]['NumeroExplotaciones'] . "</td>";
                                       echo "<td>" . $tablaGanaderiaIsla[$i]['TotalCabezas'] . "</td>";
                                       echo "</tr>";
                                       $totalCabezasIsla += $tablaGanaderiaIsla[$i]['TotalCabezas'];
                                       $totalExploGanIsla += $tablaGanaderiaIsla[$i]['NumeroExplotaciones'];
                                   }
                                   $porcentajeExplotacionesIsla = ($totalExploGanIsla / $totalExploGan) * 100;
                                   $porcentajeCabezasIsla = ($totalCabezasIsla / $totalCabezas) * 100;
                                   
                                   echo "<tr>";
                                   echo "<td style=\"text-align: right;\">Total:</td><td>" . $totalExploGanIsla . "</td><td>" . $totalCabezasIsla . "</td>";
                                   echo "</tr>";
                                   echo "<tr>";
                                   echo "<td style=\"text-align: right;\">Porcentajes respecto a la Comunidad:</td><td>" . number_format($porcentajeExplotacionesIsla, 2) ."%</td><td>" . number_format($porcentajeCabezasIsla, 2) ."%</td>";
                                   echo "</tr>";
                                   echo "</table>";
                                }
                            }
                    }
                    mysqli_close($conexion);
                }
                else{       // Si se accede mediante URL directa, se indica al usuario que debe escoger una Isla en el menú
                     echo "<h2 style=\"padding-top: 5%;\">Debe acceder a esta página mediante el uso del Menú \"<i><u>Islas</u></i>\" para poder ofrecerle la información necesaria.</h2>";
                }
            ?>
        </div>
            
        <div class="iFrameFooter"><?php include('../iFrame/Footer.php'); ?></div>
    </body>
</html>

